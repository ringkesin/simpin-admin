<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VTabunganSaldoPerYear extends Model
{
    public $table = 'v_tabungan_saldo_per_year';

    protected $casts = [
        'p_anggota_id' => 'integer',
        'tahun' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'total' => 'float',
        'total_sd' => 'float',
    ];
}
