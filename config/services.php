<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | Google Meet
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),

        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        'redirect' => env(
            'GOOGLE_REDIRECT_URI',
            'http://127.0.0.1:8000/auth/google/meet/callback'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Classroom
    |--------------------------------------------------------------------------
    */

    'google_classroom' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),

        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        'redirect' => env(
            'GOOGLE_CLASSROOM_REDIRECT_URI',
            'http://127.0.0.1:8000/auth/google/classroom/callback'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Drive
    |--------------------------------------------------------------------------
    */

    'google_drive' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),

        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        'redirect' => env(
            'GOOGLE_DRIVE_REDIRECT_URI',
            'http://127.0.0.1:8000/auth/google/drive/callback'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini AI
    |--------------------------------------------------------------------------
    */

        'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
    ],

    
  
'kapso' => [
    'api_key' => env('KAPSO_API_KEY'),
    'base_url' => env('KAPSO_BASE_URL', 'https://api.kapso.ai/meta/whatsapp'),
    'phone_number_id' => env('KAPSO_PHONE_NUMBER_ID'),
    'api_version' => env('KAPSO_API_VERSION', 'v24.0'),
    'webhook_secret' => env('KAPSO_WEBHOOK_SECRET'),

    // Sandbox safety
    'sandbox_mode' => env('KAPSO_SANDBOX_MODE', true),
    'sandbox_recipient' => env('KAPSO_SANDBOX_RECIPIENT', '923034253277'),
    'ai_auto_reply' => env('KAPSO_AI_AUTO_REPLY', false),
    'ai_live_send_enabled' => env('KAPSO_AI_LIVE_SEND_ENABLED', false),
    'ai_dry_run' => env('KAPSO_AI_DRY_RUN', false),

    // Application template safety: keep false until Kapso approves the template
    'application_template_approved' => env(
        'KAPSO_APPLICATION_TEMPLATE_APPROVED',
        false
    ),
],

];
