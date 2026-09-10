<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'callswitch' => [
        'webhook_token' => env('CALLSWITCH_WEBHOOK_TOKEN'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY', env('GOOGLE_MAP_KEY')),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY', env('GOOGLE_MAP_KEY')),
    ],

    'mysms' => [
        // Keep the current key as a fallback so existing live deployments continue
        // working; MY_SMS_API_KEY should be set in the environment going forward.
        'api_key' => env('MY_SMS_API_KEY', 'fes0Jtm5dUww0YyvQHnDsg'),
        'auth_token' => env('MY_SMS_AUTH_TOKEN'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
