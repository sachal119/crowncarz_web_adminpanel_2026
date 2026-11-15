<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class WebmailRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
    }

    public function test_ajax_urls_preserve_subdirectory_and_front_controller(): void
    {
        $template = file_get_contents(resource_path('views/emails/webmail/index.blade.php'));
        preg_match('~<script>(.*?)</script>~s', $template, $matches);

        URL::forceScheme('https');
        foreach (['https://example.test', 'https://example.test/panel', 'https://example.test/panel/index.php'] as $root) {
            URL::forceRootUrl($root);
            $script = Blade::render($matches[1], ['currentFolder' => 'INBOX']);
            // Js::from escapes slashes in JavaScript strings.
            $script = str_replace('\\/', '/', $script);
            foreach (['view', 'send', 'delete', 'star', 'message/__MESSAGE_ID__/attachment/__ATTACHMENT_INDEX__'] as $path) {
                $this->assertStringContainsString($root.'/admin/emails/'.$path, $script);
            }
            $this->assertStringNotContainsString("fetch('/admin/emails/", $script);
            $this->assertStringNotContainsString('`/admin/emails/', $script);
        }
    }

    public function test_open_and_send_routes_match_under_a_subdirectory(): void
    {
        foreach (['view' => 'GET', 'send' => 'POST'] as $action => $method) {
            $request = Request::create('https://example.test/panel/index.php/admin/emails/'.$action, $method, [], [], [], [
                'SCRIPT_NAME' => '/panel/index.php',
                'SCRIPT_FILENAME' => '/var/www/panel/index.php',
                'PHP_SELF' => '/panel/index.php/admin/emails/'.$action,
            ]);
            $route = app('router')->getRoutes()->match($request);
            $this->assertSame('emails.'.$action, $route->getName());
        }
    }

    public function test_send_reaches_validation_without_sending_mail(): void
    {
        $this->withSession(['admin_logged_in' => true])
            ->postJson(route('emails.send'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to', 'subject', 'body']);
    }
}
