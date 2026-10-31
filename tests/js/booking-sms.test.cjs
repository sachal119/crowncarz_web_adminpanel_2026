const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('resources/views/booking_form.blade.php', 'utf8');

function setup() {
    const alerts = [];
    const fields = {};
    const field = id => fields[id] ||= { value: '', disabled: false, innerHTML: id, style: {}, addEventListener(_, handler) { this.click = handler; } };
    let shown = false;
    let hidden = false;
    const context = vm.createContext({
        document: { getElementById: field, querySelector: () => ({ content: 'csrf' }) },
        alert: message => alerts.push(message), setTimeout, clearTimeout, AbortController,
        bootstrap: { Modal: {
            getOrCreateInstance: () => ({ show: () => { shown = true; } }),
            getInstance: () => ({ hide: () => { hidden = true; } })
        } },
        getFormData: () => ({ driver_name: field('currentDriverName').value }),
        replacePlaceholders: (template, data) => template.replace('{driver_name}', data.driver_name),
    });
    const prepare = source.slice(source.indexOf('async function prepareSms('), source.indexOf('// ------------------------------------', source.indexOf('async function prepareSms(')));
    const sendStart = source.indexOf('\nasync function sendSMS(');
    const send = source.slice(sendStart, source.indexOf('// ---------------------------\n// LOAD TEMPLATE', sendStart)).replace("@json(route('sms.send'))", "'/sms/send'");
    const driverStart = source.indexOf('document.getElementById("btn-driver-sms").addEventListener');
    vm.runInContext(prepare + send + source.slice(driverStart, source.indexOf('// redirect on OK', driverStart)), context);
    field('currentBookingId').value = 'booking';
    field('driver_id_select').value = 'selected-driver';
    field('currentDriverId').value = 'stale-driver';
    return { context, alerts, field, shown: () => shown, hidden: () => hidden,
        click: () => field('btn-driver-sms').click({ currentTarget: field('btn-driver-sms') }) };
}

test('preview uses selected driver and opens with their phone', async () => {
    const ui = setup();
    ui.context.readSmsData = async path => {
        assert.ok(['drivers/selected-driver', 'sms_templates/driver_details'].includes(path));
        return { exists: () => true, val: () => path.startsWith('drivers/') ? { name: 'Driver', phone: '123' } : { message: 'Hi {driver_name}' } };
    };
    await ui.click();
    assert.equal(ui.shown(), true);
    assert.equal(ui.field('sms_recipient').value, '123');
    assert.equal(ui.field('smsMessage').value, 'Hi Driver');
    assert.equal(ui.field('btn-driver-sms').disabled, false);
});

for (const failure of ['booking', 'driver', 'lookup', 'missing-driver', 'phone', 'template']) {
    test(`preview reports ${failure} failure and restores button`, async () => {
        const ui = setup();
        if (failure === 'booking') ui.field('currentBookingId').value = '';
        if (failure === 'driver') ui.field('driver_id_select').value = '';
        ui.context.readSmsData = async path => {
            if (failure === 'lookup') throw new Error('Permission denied');
            return { exists: () => failure !== 'missing-driver' && !(failure === 'template' && path.startsWith('sms_templates/')),
                val: () => ({ name: 'Driver', phone: failure === 'phone' ? '' : '123', message: 'Hello' }) };
        };
        await ui.click();
        assert.equal(ui.alerts.length, 1);
        assert.match(ui.alerts[0], /Unable to open SMS preview/);
        assert.equal(ui.shown(), false);
        assert.equal(ui.field('btn-driver-sms').disabled, false);
    });
}

for (const outcome of ['success', 'provider-error', 'validation', 'session', 'network', 'timeout', 'empty-message']) {
    test(`send reports ${outcome} and restores button`, async () => {
        const ui = setup();
        ui.context.fetch = async () => {
            if (outcome === 'network') throw new Error('Offline');
            if (outcome === 'timeout') throw Object.assign(new Error(), { name: 'AbortError' });
            return { ok: outcome === 'success', status: outcome === 'session' ? 419 : 200,
                json: async () => outcome === 'session' ? null : outcome === 'validation'
                    ? { errors: { mobile: ['Invalid phone'] } }
                    : { success: outcome === 'success', message: outcome === 'success' ? 'SMS sent successfully.' : 'Provider rejected SMS' } };
        };
        await ui.context.sendSMS('123', outcome === 'empty-message' ? ' ' : 'hello');
        assert.equal(ui.alerts.length, 1);
        assert.equal(ui.hidden(), outcome === 'success');
        assert.equal(ui.field('btn-send-sms-modal').disabled, false);
        if (outcome === 'success') assert.match(ui.alerts[0], /successfully/);
        if (outcome === 'session') assert.match(ui.alerts[0], /session has expired/);
        if (outcome === 'validation') assert.match(ui.alerts[0], /Invalid phone/);
        if (outcome === 'timeout') assert.match(ui.alerts[0], /timed out/);
    });
}

for (const outcome of ['success', 'missing', 'forbidden', 'session', 'timeout']) {
    test(`backend preview reader handles ${outcome}`, async () => {
        const ui = setup();
        const start = source.indexOf('async function readSmsData(');
        const reader = source.slice(start, source.indexOf('async function prepareSms(', start))
            .replace("@json(route('sms.preview-data'))", "'/sms/preview-data'");
        vm.runInContext(reader, ui.context);
        ui.context.fetch = async (url, options) => {
            assert.equal(url, '/sms/preview-data?path=drivers%2Fselected-driver');
            assert.equal(options.credentials, 'same-origin');
            if (outcome === 'timeout') throw Object.assign(new Error(), { name: 'AbortError' });
            return { ok: outcome !== 'forbidden', redirected: outcome === 'session', status: outcome === 'forbidden' ? 403 : 200,
                json: async () => outcome === 'forbidden' ? { message: 'Access denied' } : { data: outcome === 'missing' ? null : { name: 'Driver', phone: '123' } } };
        };
        if (['forbidden', 'session', 'timeout'].includes(outcome)) {
            await assert.rejects(ui.context.readSmsData('drivers/selected-driver'), /Access denied|session has expired|timed out/);
        } else {
            const result = await ui.context.readSmsData('drivers/selected-driver');
            assert.equal(result.exists(), outcome === 'success');
            if (outcome === 'success') assert.equal(result.val().phone, '123');
        }
    });
}
