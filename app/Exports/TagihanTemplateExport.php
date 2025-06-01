<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class TagihanTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID',
            'Nomor Anggota',
            'Nomor Pinjaman',
            'Bulan',
            'Tahun',
            'Uraian',
            'Jumlah Tagihan',
            'Remarks',
            'Tgl Jatuh Tempo',
            'Status Pembayaran',
            'Tgl Dibayar',
            'Jumlah Dibayarkan',
            'Metode Pembayaran'
        ];
    }

    public function array(): array
    {
        return [
            ['', '100001', '001/PJ/UM/V/2025', '1', '2025', 'Cicilan Pinjaman', '1000000', '', '2025-05-11', 'paid', '2025-05-11', '1000000', 'tongji'], // Contoh data
            ['', '100002', '001/PJ/UM/V/2025', '12', '2024', 'DP Motor', '1000000', '', '2025-05-11', 'paid', '2025-05-11', '1000000', 'tf'], // Contoh data
        ];
    }
}
