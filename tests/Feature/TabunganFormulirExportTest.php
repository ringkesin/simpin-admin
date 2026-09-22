<?php

namespace Tests\Feature;

use App\Services\MemberQrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;

class TabunganFormulirExportTest extends TestCase
{
    public function test_formulir_uses_member_number_qr_code_as_electronic_signature(): void
    {
        $qrDataUri = app(MemberQrCodeService::class)->dataUri('00012345');

        $this->assertNotNull($qrDataUri);
        $this->assertStringStartsWith('data:image/png;base64,', $qrDataUri);
        $this->assertStringStartsWith("\x89PNG", base64_decode(substr($qrDataUri, strlen('data:image/png;base64,'))));

        $html = view('livewire.page.main.export.tabungan-formulir-export', [
            'nama_anggota' => 'Anggota KKBA',
            'nomor_anggota' => '00012345',
            'nik' => '123456',
            'mobile' => '08123456789',
            'tgl_pengajuan' => '23 September 2026',
        ])->render();

        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringNotContainsString('No. Anggota: 00012345', $html);
        $this->assertStringNotContainsString('Diajukan secara elektronik melalui aplikasi KKBA Mobile', $html);

        $this->assertStringStartsWith('%PDF', Pdf::loadHTML($html)->output());
    }
}
