<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VPenyertaanTabungan extends Model
{
    public $table = 'v_penyertaan_tabungan';

    protected $casts = [
        'penyertaan_id' => 'string',
        'p_anggota_id' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'penyertaan_date' => 'date',
        'jumlah' => 'float',
    ];
}
