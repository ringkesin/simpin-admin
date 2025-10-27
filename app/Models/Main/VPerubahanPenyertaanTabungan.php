<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VPerubahanPenyertaanTabungan extends Model
{
    public $table = 'v_perubahan_penyertaan_tabungan';

    protected $casts = [
        'perubahan_penyertaan_id' => 'string',
        'p_anggota_id' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'valid_from' => 'date',
        'nilai_sebelum' => 'float',
        'nilai_baru' => 'float'
    ];
}
