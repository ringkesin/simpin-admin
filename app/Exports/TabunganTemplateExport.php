<?php

namespace App\Exports;

use App\Models\Main\TabunganTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class TabunganTemplateExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return TabunganTransaction::all();
    // }
    public function headings(): array
    {
        return [
            'nomor_anggota',
            'bulan',
            'tahun',
            'simpanan_pokok',
            'simpanan_wajib',
            'tabungan_sukarela',
            'tabungan_indir',
            'kompensasi_masa_kerja',
        ];
    }

    public function array(): array
    {
        return [
            ['100001', '1', '2025', '1000000', '1000000', '1000000', '1000000', '1000000'], // Contoh data
            ['100002', '1', '2025', '1000000', '1000000', '1000000', '1000000', '1000000'], // Contoh data
        ];
    }
}
