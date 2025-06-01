<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\Main\TagihanModels;
use Illuminate\Support\Facades\DB;

class TagihanExport implements FromCollection, WithHeadings
{
    protected $bulan;
    protected $tahun;

    // public function headings(): array
    // {
    //     return [
    //         'nomor_anggota',
    //         'nomor_pinjaman',
    //         'bulan',
    //         'tahun',
    //         'uraian',
    //         'jumlah_tagihan',
    //         'remarks',
    //         'tgl_jatuh_tempo',
    //         'status_pembayaran',
    //         'tgl_dibayar',
    //         'jumlah_dibayarkan',
    //         'metode_pembayaran'
    //     ];
    // }

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return DB::table('t_tagihan')
                ->where('t_tagihan.bulan', $this->bulan)
                ->where('t_tagihan.tahun', $this->tahun)
                ->leftJoin('p_anggota', 'p_anggota.p_anggota_id', '=', 't_tagihan.p_anggota_id')
                ->leftJoin('t_pinjaman', 't_pinjaman.t_pinjaman_id', '=', 't_tagihan.t_pinjaman_id')
                ->leftJoin('p_status_pembayaran', 'p_status_pembayaran.p_status_pembayaran_id', '=', 't_tagihan.p_status_pembayaran_id')
                ->leftJoin('p_metode_pembayaran', 'p_metode_pembayaran.p_metode_pembayaran_id', '=', 't_tagihan.p_metode_pembayaran_id')
                ->select(
                    't_tagihan.t_tagihan_id',
                    'p_anggota.nomor_anggota',
                    't_pinjaman.nomor_pinjaman',
                    't_tagihan.bulan',
                    't_tagihan.tahun',
                    't_tagihan.uraian',
                    't_tagihan.jumlah_tagihan',
                    't_tagihan.remarks',
                    't_tagihan.tgl_jatuh_tempo',
                    'p_status_pembayaran.status_code',
                    't_tagihan.paid_at',
                    't_tagihan.jumlah_pembayaran',
                    'p_metode_pembayaran.metode_code'
                )
                ->whereNull('t_tagihan.deleted_at')
                ->get();
    }

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
}
