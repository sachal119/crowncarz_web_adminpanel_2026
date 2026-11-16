<?php

// Info Mailbox (info@crowncarz.com)
$infoImapUsername = env('HOSTINGER_WEBMAIL_IMAP_USERNAME', env('IMAP_USERNAME', env('MAIL_USERNAME', 'info@crowncarz.com')));
$infoImapPassword = env('HOSTINGER_WEBMAIL_IMAP_PASSWORD', env('IMAP_PASSWORD', env('MAIL_PASSWORD', '')));

// Accounts Mailbox (accounts@crowncarz.com)
$accountsUsername = env('HOSTINGER_ACCOUNTS_EMAIL_USERNAME', env('ACCOUNTS_EMAIL_USERNAME', 'accounts@crowncarz.com'));
$accountsPassword = env('HOSTINGER_ACCOUNTS_EMAIL_PASSWORD', env('HOSTINGER_ACCOUNTS_WEBMAIL_PASSWORD', env('ACCOUNTS_EMAIL_PASSWORD', env('ACCOUNTS_MAIL_PASSWORD', ''))));

return [
    /*
    |--------------------------------------------------------------------------
    | Hostinger Webmail Configurations
    |--------------------------------------------------------------------------
    |
    | Dedicated business mailboxes for Crown Carz:
    | 1) info@crowncarz.com (General & customer inquiries)
    | 2) accounts@crowncarz.com (Finance, billing & driver accounts)
    |
    */

    'accounts' => [
        'info' => [
            'name' => 'Crown Carz',
            'email' => 'info@crowncarz.com',
            'imap' => [
                'host' => env('HOSTINGER_WEBMAIL_IMAP_HOST', env('IMAP_HOST', 'imap.hostinger.com')),
                'port' => (int) env('HOSTINGER_WEBMAIL_IMAP_PORT', env('IMAP_PORT', 993)),
                'encryption' => env('HOSTINGER_WEBMAIL_IMAP_ENCRYPTION', env('IMAP_ENCRYPTION', 'ssl')),
                'validate_cert' => env('HOSTINGER_WEBMAIL_IMAP_VALIDATE_CERT', false),
                'username' => $infoImapUsername,
                'password' => $infoImapPassword,
                'timeout' => 15,
            ],
            'smtp' => [
                'host' => env('HOSTINGER_WEBMAIL_SMTP_HOST', 'smtp.hostinger.com'),
                'port' => (int) env('HOSTINGER_WEBMAIL_SMTP_PORT', 465),
                'encryption' => env('HOSTINGER_WEBMAIL_SMTP_ENCRYPTION', 'ssl'),
                'username' => env('HOSTINGER_WEBMAIL_SMTP_USERNAME', $infoImapUsername),
                'password' => env('HOSTINGER_WEBMAIL_SMTP_PASSWORD', $infoImapPassword),
                'from_address' => env('HOSTINGER_WEBMAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'info@crowncarz.com')),
                'from_name' => env('HOSTINGER_WEBMAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Crown Carz')),
                'timeout' => 15,
            ],
            'mailer' => 'hostinger_webmail',
        ],

        'accounts' => [
            'name' => 'Crown Carz Accounts',
            'email' => 'accounts@crowncarz.com',
            'imap' => [
                'host' => env('HOSTINGER_ACCOUNTS_IMAP_HOST', 'imap.hostinger.com'),
                'port' => (int) env('HOSTINGER_ACCOUNTS_IMAP_PORT', 993),
                'encryption' => env('HOSTINGER_ACCOUNTS_IMAP_ENCRYPTION', 'ssl'),
                'validate_cert' => env('HOSTINGER_ACCOUNTS_IMAP_VALIDATE_CERT', false),
                'username' => $accountsUsername,
                'password' => $accountsPassword,
                'timeout' => 15,
            ],
            'smtp' => [
                'host' => env('HOSTINGER_ACCOUNTS_SMTP_HOST', 'smtp.hostinger.com'),
                'port' => (int) env('HOSTINGER_ACCOUNTS_SMTP_PORT', 465),
                'encryption' => env('HOSTINGER_ACCOUNTS_SMTP_ENCRYPTION', 'ssl'),
                'username' => env('HOSTINGER_ACCOUNTS_SMTP_USERNAME', $accountsUsername),
                'password' => env('HOSTINGER_ACCOUNTS_SMTP_PASSWORD', $accountsPassword),
                'from_address' => env('HOSTINGER_ACCOUNTS_FROM_ADDRESS', 'accounts@crowncarz.com'),
                'from_name' => env('HOSTINGER_ACCOUNTS_FROM_NAME', 'Crown Carz Accounts'),
                'timeout' => 15,
            ],
            'mailer' => 'hostinger_accounts_webmail',
        ],
    ],

    // Backward-compatibility aliases for existing code calling config('webmail.imap')
    'imap' => [
        'host' => env('HOSTINGER_WEBMAIL_IMAP_HOST', env('IMAP_HOST', 'imap.hostinger.com')),
        'port' => (int) env('HOSTINGER_WEBMAIL_IMAP_PORT', env('IMAP_PORT', 993)),
        'encryption' => env('HOSTINGER_WEBMAIL_IMAP_ENCRYPTION', env('IMAP_ENCRYPTION', 'ssl')),
        'validate_cert' => env('HOSTINGER_WEBMAIL_IMAP_VALIDATE_CERT', false),
        'username' => $infoImapUsername,
        'password' => $infoImapPassword,
        'timeout' => 15,
    ],

    'smtp' => [
        'host' => env('HOSTINGER_WEBMAIL_SMTP_HOST', 'smtp.hostinger.com'),
        'port' => (int) env('HOSTINGER_WEBMAIL_SMTP_PORT', 465),
        'encryption' => env('HOSTINGER_WEBMAIL_SMTP_ENCRYPTION', 'ssl'),
        'username' => env('HOSTINGER_WEBMAIL_SMTP_USERNAME', $infoImapUsername),
        'password' => env('HOSTINGER_WEBMAIL_SMTP_PASSWORD', $infoImapPassword),
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
