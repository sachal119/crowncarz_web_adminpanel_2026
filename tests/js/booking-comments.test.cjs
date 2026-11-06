const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('resources/views/dashboard.blade.php', 'utf8');
const start = source.indexOf('function updateBookingRowData(booking)');
const end = source.indexOf('// 🗑️ Remove Row', start);

function setup() {
    const comment = { textContent: 'Old comment', title: 'Old comment' };
    const attributes = {};
    const viewButton = { setAttribute: (key, value) => { attributes[key] = value; } };
    const row = { querySelector: selector => ({
        '.col-comment .truncate-cell': comment,
        '.view-booking-btn': viewButton,
    })[selector] || null };
    const context = vm.createContext({ document: {
        getElementById: id => id === 'booking-row-test' ? row : null,
    } });
    vm.runInContext(source.slice(start, end), context);
    return { comment, attributes, update: context.updateBookingRowData };
}

test('edited comments update the visible text, tooltip, and booking modal data', () => {
    const ui = setup();
    const booking = { id: 'test', job_comment: 'New instructions <b>& luggage</b>\nDoor 2' };
    ui.update(booking);
    assert.equal(ui.comment.textContent, booking.job_comment);
    assert.equal(ui.comment.title, booking.job_comment);
    assert.deepEqual(JSON.parse(ui.attributes['data-booking']), booking);
});

for (const value of ['', '   ', null, undefined]) {
    test(`clearing comments resets the displayed text (${JSON.stringify(value)})`, () => {
        const ui = setup();
        ui.update({ id: 'test', job_comment: value });
        assert.equal(ui.comment.textContent, 'No comments added');
        assert.equal(ui.comment.title, 'No comments added');
    });
}

test('legacy comments display when job_comment is absent', () => {
    const ui = setup();
    ui.update({ id: 'test', comments: 'Legacy instructions' });
    assert.equal(ui.comment.textContent, 'Legacy instructions');
});

test('an explicitly empty job_comment takes precedence over legacy comments', () => {
    const ui = setup();
    ui.update({ id: 'test', job_comment: '', comments: 'Old legacy instructions' });
    assert.equal(ui.comment.textContent, 'No comments added');
});

test('updates for rows outside the current page are ignored', () => {
    const ui = setup();
    assert.doesNotThrow(() => ui.update({ id: 'other', job_comment: 'Other booking' }));
    assert.equal(ui.comment.textContent, 'Old comment');
});
