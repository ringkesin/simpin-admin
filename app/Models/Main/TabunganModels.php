<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\AnggotaModels;

class TabunganModels extends Model
{
    use HasFactory;

    protected $table = 't_tabungan';

    protected $primaryKey = 't_tabungan_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'tabungan_sukarela',
        'tabungan_indir',
        'kompensasi_masa_kerja',
        'bulan',
        'tahun',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

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
}
