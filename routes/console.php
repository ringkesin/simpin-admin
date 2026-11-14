<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Minishlink\WebPush\VAPID;

Artisan::command('push:web-vapid', function () {
    $keys = VAPID::createVapidKeys();
    $this->line('WEB_PUSH_VAPID_PUBLIC_KEY='.$keys['publicKey']);
    $this->line('WEB_PUSH_VAPID_PRIVATE_KEY='.$keys['privateKey']);
    $this->comment('Simpan private key sebagai secret backend. Jalankan sekali per environment; jangan rotasi saat deployment.');
})->purpose('Generate VAPID keys explicitly for operator setup');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
