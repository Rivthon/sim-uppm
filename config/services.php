<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'pas' => [
        'driver' => env('PAS_DRIVER', 'mock'),
        'endpoint' => env('PAS_AUTH_ENDPOINT'),
        'token' => env('PAS_API_TOKEN'),
        'timeout' => (int) env('PAS_TIMEOUT', 10),
        'username_field' => env('PAS_USERNAME_FIELD', 'username'),
        'password_field' => env('PAS_PASSWORD_FIELD', 'password'),
        'mock_username' => env('PAS_MOCK_USERNAME', 'admin.uppm'),
        'mock_password' => env('PAS_MOCK_PASSWORD', 'uppm12345'),
    ],

];
