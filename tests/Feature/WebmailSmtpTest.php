<?php

namespace Tests\Feature;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class WebmailSmtpTest extends TestCase
{
    public function test_smtp_uses_mailbox_credentials_and_respects_explicit_overrides(): void
    {
        $repository = Env::getRepository();
        $values = [
            'HOSTINGER_WEBMAIL_IMAP_USERNAME' => null,
            'HOSTINGER_WEBMAIL_IMAP_PASSWORD' => null,
            'HOSTINGER_WEBMAIL_SMTP_USERNAME' => null,
            'HOSTINGER_WEBMAIL_SMTP_PASSWORD' => null,
            'IMAP_USERNAME' => 'mailbox@example.test',
            'IMAP_PASSWORD' => 'mailbox-test-password',
            'MAIL_USERNAME' => 'transactional@example.test',
            'MAIL_PASSWORD' => 'different-test-password',
        ];
        $original = [];
        foreach ($values as $key => $value) {
            $original[$key] = $repository->get($key);
            $repository->clear($key);
            if ($value !== null) {
                $repository->set($key, $value);
            }
        }

        try {
            $webmail = require config_path('webmail.php');
            $mail = require config_path('mail.php');
            $this->assertSame('mailbox@example.test', $webmail['smtp']['username']);
            $this->assertSame('mailbox-test-password', $webmail['smtp']['password']);
            $this->assertSame($webmail['smtp']['username'], $mail['mailers']['hostinger_webmail']['username']);
            $this->assertSame($webmail['imap']['password'], $mail['mailers']['hostinger_webmail']['password']);
            $this->assertSame('different-test-password', $mail['mailers']['smtp']['password']);

            $repository->set('HOSTINGER_WEBMAIL_SMTP_USERNAME', 'outgoing@example.test');
            $repository->set('HOSTINGER_WEBMAIL_SMTP_PASSWORD', 'explicit-test-password');
            $mail = require config_path('mail.php');
            $this->assertSame('outgoing@example.test', $mail['mailers']['hostinger_webmail']['username']);
            $this->assertSame('explicit-test-password', $mail['mailers']['hostinger_webmail']['password']);
        } finally {
            foreach ($original as $key => $value) {
                $repository->clear($key);
                if ($value !== null) {
                    $repository->set($key, $value);
                }
            }
        }
    }

    public function test_smtp_authentication_failure_returns_a_clear_error(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        $mailer = \Mockery::mock(\Illuminate\Mail\Mailer::class);
        $mailer->shouldReceive('html')->once()
            ->andThrow(new TransportException('535 5.7.8 Authentication failed: private server diagnostics'));
        Mail::shouldReceive('mailer')->once()->with('hostinger_webmail')->andReturn($mailer);

        $this->withSession(['admin_logged_in' => true])->postJson(route('emails.send'), [
            'to' => 'recipient@example.test',
            'subject' => 'Test',
            'body' => 'Test body',
        ])->assertStatus(502)->assertJson([
            'success' => false,
            'error' => 'Email server login failed. Please ask your administrator to verify the outgoing mailbox username and password.',
        ])->assertDontSee('private server diagnostics');
    }
}
