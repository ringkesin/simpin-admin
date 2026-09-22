<?php

namespace App\Services;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class MemberQrCodeService
{
    public function dataUri(?string $nomorAnggota): ?string
    {
        $nomorAnggota = trim((string) $nomorAnggota);

        if ($nomorAnggota === '') {
            return null;
        }

        $renderer = new GDLibRenderer(160, 2, 'png');
        $png = (new Writer($renderer))->writeString($nomorAnggota);

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
