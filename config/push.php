<?php

return [
    'delivery_enabled' => (bool) env('PUSH_DELIVERY_ENABLED', false),
    'http_timeout' => (int) env('PUSH_HTTP_TIMEOUT', 5),
    'connect_timeout' => (int) env('PUSH_CONNECT_TIMEOUT', 2),
    'max_provider_payload_bytes' => 4096,

    'expo' => [
        'endpoint' => 'https://exp.host/--/api/v2/push/send',
        'access_token' => env('EXPO_PUSH_ACCESS_TOKEN'),
        'batch_size' => 100,
    ],

    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID'),
        'service_account_base64' => env('FCM_SERVICE_ACCOUNT_BASE64'),
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
    ],

    'internal' => [
        'mart_client_id' => env('MART_NOTIFICATION_CLIENT_ID', 'mart'),
        'mart_secret' => env('MART_NOTIFICATION_SECRET'),
        'allowed_skew_seconds' => (int) env('INTERNAL_NOTIFICATION_ALLOWED_SKEW_SECONDS', 300),
        'max_payload_bytes' => 32 * 1024,
        'rate_limit_per_minute' => 120,
    ],
];
