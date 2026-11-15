<?php

// Use the same mailbox credentials for reading and sending unless SMTP is explicitly overridden.
$imapUsername = env('HOSTINGER_WEBMAIL_IMAP_USERNAME', env('IMAP_USERNAME', env('MAIL_USERNAME', 'info@crowncarz.com')));
$imapPassword = env('HOSTINGER_WEBMAIL_IMAP_PASSWORD', env('IMAP_PASSWORD', env('MAIL_PASSWORD', '')));

return [
    /*
    |--------------------------------------------------------------------------
    | Hostinger Webmail Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Crown Carz Hostinger Business Email (info@crowncarz.com).
    | Designed to be completely isolated from transactional app mailers.
    |
    */

    'imap' => [
        'host' => env('HOSTINGER_WEBMAIL_IMAP_HOST', env('IMAP_HOST', 'imap.hostinger.com')),
        'port' => (int) env('HOSTINGER_WEBMAIL_IMAP_PORT', env('IMAP_PORT', 993)),
        'encryption' => env('HOSTINGER_WEBMAIL_IMAP_ENCRYPTION', env('IMAP_ENCRYPTION', 'ssl')),
        'validate_cert' => env('HOSTINGER_WEBMAIL_IMAP_VALIDATE_CERT', false),
        'username' => $imapUsername,
        'password' => $imapPassword,
        'timeout' => 15,
    ],

    'smtp' => [
        'host' => env('HOSTINGER_WEBMAIL_SMTP_HOST', 'smtp.hostinger.com'),
        'port' => (int) env('HOSTINGER_WEBMAIL_SMTP_PORT', 465),
        'encryption' => env('HOSTINGER_WEBMAIL_SMTP_ENCRYPTION', 'ssl'),
        'username' => env('HOSTINGER_WEBMAIL_SMTP_USERNAME', $imapUsername),
        'password' => env('HOSTINGER_WEBMAIL_SMTP_PASSWORD', $imapPassword),
        'from_address' => env('HOSTINGER_WEBMAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'info@crowncarz.com')),
        'from_name' => env('HOSTINGER_WEBMAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Crown Carz')),
        'timeout' => 15,
    ],

    'cache' => [
        'enabled' => true,
        'ttl' => 60, // seconds
    ],

    'attachments' => [
        'max_size_mb' => 20,
        'allowed_types' => [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar', '7z'
        ],
    ],
];
