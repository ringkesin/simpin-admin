<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VTabunganJurnal extends Model
{
    public $table = 'v_tabungan_jurnal';

    protected $casts = [
        'p_anggota_id' => 'integer',
        'tgl_transaksi' => 'datetime',
        'bulan_transaksi' => 'integer',
        'tahun_transaksi' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'nilai' => 'float',
        'nilai_sd' => 'float',
    ];
}
