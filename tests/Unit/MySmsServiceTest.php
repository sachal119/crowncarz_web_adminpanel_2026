<?php

namespace Tests\Unit;

use App\Services\MySmsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MySmsServiceTest extends TestCase
{
    public function test_it_reports_a_successful_provider_response_as_success(): void
    {
        config()->set('services.mysms', [
            'api_key' => 'test-key',
            'auth_token' => 'test-token',
        ]);
        Http::fake([
            'api.mysms.com/*' => Http::response(['errorCode' => 0], 200),
        ]);

        $result = app(MySmsService::class)->send('07412424522', 'Test message');

        $this->assertTrue($result['success']);
        Http::assertSent(fn ($request) => $request['recipients'] === ['07412424522']
            && $request['message'] === 'Test message'
            && $request['apiKey'] === 'test-key'
            && $request['authToken'] === 'test-token');
    }

    public function test_it_does_not_claim_success_when_the_provider_rejects_the_sms(): void
    {
        config()->set('services.mysms', [
            'api_key' => 'test-key',
            'auth_token' => 'test-token',
        ]);
        Http::fake([
            'api.mysms.com/*' => Http::response([
                'errorCode' => 98,
                'message' => 'Invalid recipient',
            ], 200),
        ]);

        $result = app(MySmsService::class)->send('invalid', 'Test message');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid recipient', $result['message']);
    }

    public function test_it_fails_without_sending_when_credentials_are_missing(): void
    {
        config()->set('services.mysms', [
            'api_key' => null,
            'auth_token' => null,
        ]);
        Http::fake();

        $result = app(MySmsService::class)->send('07412424522', 'Test message');

        $this->assertFalse($result['success']);
        Http::assertNothingSent();
    }
}
