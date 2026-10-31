<?php

namespace Tests\Feature;

use App\Services\FirebaseService;
use Tests\TestCase;

class SmsPreviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
    }

    public function test_guests_cannot_read_preview_data(): void
    {
        $this->mock(FirebaseService::class)->shouldNotReceive('getData');
        $this->getJson('/sms/preview-data?path=drivers/driver-1')->assertRedirect(route('login'));
    }

    public function test_booking_collaborators_can_read_only_driver_preview_fields(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->once()->with('drivers/driver-1')
            ->andReturn(['name' => 'Driver', 'phone' => '123', 'password' => 'secret', 'documents' => ['private']]);
        $this->withSession(['admin_logged_in' => true, 'staff_role' => 'collaborator'])
            ->getJson('/sms/preview-data?path=drivers/driver-1')->assertOk()
            ->assertExactJson(['data' => ['name' => 'Driver', 'phone' => '123']]);
    }

    public function test_templates_load_through_server(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->once()->with('sms_templates/driver_details')
            ->andReturn(['message' => 'Hi {driver_name}', 'internal' => 'private']);
        $this->withSession(['admin_logged_in' => true])->getJson('/sms/preview-data?path=sms_templates/driver_details')
            ->assertOk()->assertExactJson(['data' => ['message' => 'Hi {driver_name}']]);
    }

    public function test_arbitrary_database_paths_are_rejected(): void
    {
        $this->mock(FirebaseService::class)->shouldNotReceive('getData');
        foreach (['drivers', 'drivers/id/password', 'drivers/../settings', 'system_settings', 'sms_templates/unknown'] as $path) {
            $this->withSession(['admin_logged_in' => true])->getJson('/sms/preview-data?path='.urlencode($path))
                ->assertUnprocessable();
        }
    }

    public function test_missing_record_returns_null(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andReturnNull();
        $this->withSession(['admin_logged_in' => true])->getJson('/sms/preview-data?path=drivers/missing')
            ->assertOk()->assertExactJson(['data' => null]);
    }

    public function test_backend_failure_returns_safe_error(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andThrow(new \RuntimeException('private credentials'));
        $this->withSession(['admin_logged_in' => true])->getJson('/sms/preview-data?path=drivers/id')
            ->assertStatus(503)->assertJsonStructure(['message'])->assertDontSee('private credentials');
    }
}
