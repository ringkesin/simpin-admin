<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Main\TabunganJurnalModels;
use App\Models\Main\TabunganSaldoModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MobileDemoSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        TabunganJurnalModels::insert([
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>1000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>2000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>3000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>4000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>5000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>6000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>7000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>8000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>9000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>10000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>11000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>12000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>1,'nilai'=>1000000,'nilai_sd'=>13000000],

            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>7950000,'nilai_sd'=>7950000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8050000,'nilai_sd'=>16000000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8150000,'nilai_sd'=>24150000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8250000,'nilai_sd'=>32400000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8350000,'nilai_sd'=>40750000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8450000,'nilai_sd'=>49200000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8550000,'nilai_sd'=>57750000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8650000,'nilai_sd'=>66400000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8650000,'nilai_sd'=>75050000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8750000,'nilai_sd'=>83800000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8850000,'nilai_sd'=>92650000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8850000,'nilai_sd'=>101500000],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>2,'nilai'=>8950000,'nilai_sd'=>110450000],

            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2341140,'nilai_sd'=>2341140],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2391140,'nilai_sd'=>4732280],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2441140,'nilai_sd'=>7173420],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2491140,'nilai_sd'=>9664560],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2541140,'nilai_sd'=>12205700],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2591140,'nilai_sd'=>14796840],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2641140,'nilai_sd'=>17437980],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2691140,'nilai_sd'=>20129120],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2691140,'nilai_sd'=>22820260],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2591140,'nilai_sd'=>25411400],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2641140,'nilai_sd'=>28052540],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2641140,'nilai_sd'=>30693680],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>3,'nilai'=>2691140,'nilai_sd'=>33384820],

            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>664662,'nilai_sd'=>664662],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>664662,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>4,'nilai'=>0,'nilai_sd'=>1329324],

            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
            ['t_tabungan_jurnal_id'=>strtolower(Str::ulid()),'p_anggota_id' => 323, 'tgl_transaksi' => date('Y-m-d H:i:s'), 'p_jenis_tabungan_id'=>5,'nilai'=>0,'nilai_sd'=>0],
        ]);

        TabunganSaldoModels::insert([
            [
                't_tabungan_saldo_id' => strtolower(Str::ulid()),
                'p_anggota_id' => 323,
                'p_jenis_tabungan_id' => 1,
                'tahun' => 2024,
                'total_sd' => 13000000,
            ]
        ]);

        TabunganSaldoModels::insert([
            [
                't_tabungan_saldo_id' => strtolower(Str::ulid()),
                'p_anggota_id' => 323,
                'p_jenis_tabungan_id' => 2,
                'tahun' => 2024,
                'total_sd' => 110450000,
            ]
        ]);

        TabunganSaldoModels::insert([
            [
                't_tabungan_saldo_id' => strtolower(Str::ulid()),
                'p_anggota_id' => 323,
                'tahun' => 2024,
                'p_jenis_tabungan_id' => 3,
                'total_sd' => 33384820,
            ]
        ]);

        TabunganSaldoModels::insert([
            [
                't_tabungan_saldo_id' => strtolower(Str::ulid()),
                'p_anggota_id' => 323,
                'tahun' => 2024,
                'p_jenis_tabungan_id' => 4,
                'total_sd' => 1329324,
            ]
        ]);

        TabunganSaldoModels::insert([
            [
                't_tabungan_saldo_id' => strtolower(Str::ulid()),
                'p_anggota_id' => 323,
                'tahun' => 2024,
                'p_jenis_tabungan_id' => 5,
                'total_sd' => 0,
            ]
        ]);

        DB::commit();
    }
}
