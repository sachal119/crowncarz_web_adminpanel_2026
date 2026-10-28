<?php

namespace Tests\Feature;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AddressSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        Http::preventStrayRequests();
    }

    public function test_search_uses_saved_key_server_side_and_returns_only_results(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')
            ->once()->with('system_settings/autocomplete_key')->andReturn(' saved-key ');
        $results = [['DPA' => ['ADDRESS' => 'FLAT, BASILDON PARK, RG8 9NR', 'LAT' => 51.5, 'LNG' => -1.1]]];
        Http::fake(['api.os.uk/*' => Http::response(['header' => ['uri' => 'secret'], 'results' => $results])]);

        $this->getJson('/api/address-search?query=RG8%209NR')
            ->assertOk()->assertExactJson(['results' => $results]);

        Http::assertSent(fn ($request) => $request['key'] === 'saved-key'
            && $request['query'] === 'RG8 9NR'
            && $request['output_srs'] === 'EPSG:4326'
            && $request['maxresults'] === 10);
    }

    public function test_error_object_is_never_used_as_a_key(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andReturn(['error' => 'Permission denied']);
        Http::fake();
        $this->getJson('/api/address-search?query=RG8%209NR')->assertStatus(503)->assertJsonStructure(['message']);
        Http::assertNothingSent();
    }

    public function test_invalid_queries_do_not_call_the_provider(): void
    {
        $this->mock(FirebaseService::class)->shouldNotReceive('getData');
        Http::fake();
        $this->getJson('/api/address-search?query=RG')->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_provider_errors_are_reported_without_leaking_credentials(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andReturn('secret-key');
        Http::fake(['api.os.uk/*' => Http::response(['error' => 'secret-key'], 401)]);
        $this->getJson('/api/address-search?query=RG8%209NR')
            ->assertStatus(502)->assertJsonStructure(['message'])->assertDontSee('secret-key');
    }

    public function test_firebase_failure_returns_a_safe_error(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andThrow(new \RuntimeException('secret-key'));
        Http::fake();
        $this->getJson('/api/address-search?query=RG8%209NR')
            ->assertStatus(503)->assertDontSee('secret-key');
        Http::assertNothingSent();
    }

    public function test_no_matches_is_a_successful_empty_result(): void
    {
        $this->mock(FirebaseService::class)->shouldReceive('getData')->andReturn('saved-key');
        Http::fake(['api.os.uk/*' => Http::response(['header' => ['totalresults' => 0]])]);
        $this->getJson('/api/address-search?query=RG8%209NR')->assertOk()->assertExactJson(['results' => []]);
    }
}
