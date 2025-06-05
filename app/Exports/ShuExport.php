<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\Main\ShuModels;
use Illuminate\Support\Facades\DB;

class ShuExport implements FromCollection, WithHeadings
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return DB::table('t_shu')
                ->where('t_shu.tahun', $this->tahun)
                ->leftJoin('p_anggota', 'p_anggota.p_anggota_id', '=', 't_shu.p_anggota_id')
                ->select(
                    'p_anggota.nomor_anggota',
                    't_shu.tahun',
                    't_shu.shu_diterima'
                )
                ->whereNull('t_shu.deleted_at')
                ->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Anggota',
            'Tahun',
            'SHU Diterima'
        ];
    }
}
