<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class VTabunganSaldo extends Model
{
    public $table = 'v_tabungan_saldo';

    protected $casts = [
        'p_anggota_id' => 'integer',
        'total_tabungan' => 'float'
    ];
}
