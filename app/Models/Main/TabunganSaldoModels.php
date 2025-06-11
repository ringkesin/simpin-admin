<?php

namespace App\Models\Main;

use App\Models\User;
use App\Models\Master\AnggotaModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\JenisTabunganModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TabunganSaldoModels extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;

    protected $table = 't_tabungan_saldo';
    protected $primaryKey = 't_tabungan_saldo_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'p_jenis_tabungan_id',
        'tahun',
        'total',
        'total_sd',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'total_sd' => 'float',
        'p_jenis_tabungan_id' => 'integer',
        'p_anggota_id' => 'integer',
        'tahun' => 'integer'
    ];

    // public function getNilaiFormattedAttribute()
    // {
    //     return number_format($this->nilai, 2, ',', '.');
    // }

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function masterAnggota(): HasOne
    {
        return $this->hasOne(AnggotaModels::class, 'p_anggota_id', 'p_anggota_id');
    }

    public function jenisTabungan(): HasOne
    {
        return $this->hasOne(JenisTabunganModels::class, 'p_jenis_tabungan_id', 'p_jenis_tabungan_id');
    }
}
