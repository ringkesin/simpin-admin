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
            'nomor_anggota',
            'bulan',
            'tahun',
            'uraian',
            'jumlah',
            'remarks'
        ];
    }

    public function array(): array
    {
        return [
            ['100001', '1', '2025', 'Cicilan Pinjaman', '1000000', ''], // Contoh data
            ['100002', '12', '2024', 'DP Motor', '1000000', ''], // Contoh data
        ];
    }
}
