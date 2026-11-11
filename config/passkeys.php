<?php

return [
    'enabled' => (bool) env('PWA_PASSKEYS_ENABLED', false),
    'rp_id' => env('PWA_PASSKEYS_RP_ID', 'koperasi-abipraya.com'),
    'rp_name' => 'KKBA Mobile',
    'origins' => array_filter(array_map('trim', explode(',', env('PWA_PASSKEYS_ORIGINS', 'https://koperasi-abipraya.com')))),
    'challenge_seconds' => 300,
    'token_hours' => 8,
];
