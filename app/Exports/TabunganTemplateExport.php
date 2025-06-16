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
            'tgl_transaksi (yyyy-mm-dd)',
            'tabungan_id',
            'remarks',
            'nilai',
        ];
    }

    public function array(): array
    {
        return [
            ['100001', '2025-06-16', '1', 'Lorem ipsum', '100000'], // Contoh data
            ['100001', '2025-06-16', '3', 'dolor sit amet', '20000'], // Contoh data
            ['100002', '2025-06-16', '1', '', '500000'], // Contoh data
            ['100002', '2025-06-16', '3', 'Testing Keterangan', '100000'], // Contoh data
        ];
    }
}
