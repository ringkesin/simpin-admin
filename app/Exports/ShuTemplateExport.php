<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class ShuTemplateExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'Nomor Anggota',
            'Tahun',
            'SHU Diterima',
            // 'shu_dibagi',
            // 'shu_ditabung',
            // 'shu_tahun_lalu'
        ];
    }

    public function array(): array
    {
        // return [
        //     ['100001', '2025', '1000000', '1000000', '1000000', '1000000'], // Contoh data
        //     ['100001', '2024', '1000000', '1000000', '1000000', '1000000'], // Contoh data
        // ];
         return [
            ['100001', '2025', '1000000'], // Contoh data
            ['100001', '2024', '1000000'], // Contoh data
        ];
    }
}
