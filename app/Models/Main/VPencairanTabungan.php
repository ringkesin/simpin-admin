<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VPencairanTabungan extends Model
{
    public $table = 'v_pencairan_tabungan';

    protected $casts = [
        'pencairan_id' => 'string',
        'p_anggota_id' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'tgl_pengajuan' => 'date',
        'jumlah_diambil' => 'float',
        'jumlah_disetujui' => 'float',
        'tgl_pencairan' => 'date',
    ];
}
