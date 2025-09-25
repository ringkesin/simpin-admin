<?php

namespace App\Models\Main;

use App\Models\User;
use App\Models\Master\AnggotaModels;
use App\Models\Master\JenisTabunganModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class TabunganPerubahanPenyertaanModels extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;

    protected $table = 't_tabungan_perubahan_penyertaan';
    protected $primaryKey = 't_tabungan_perubahan_penyertaan_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'p_jenis_tabungan_id',
        'nilai_sebelum',
        'nilai_baru',
        'valid_from',
        'catatan_user',
        'catatan_approver',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'p_anggota_id' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'valid_from' => 'datetime',
        'nilai_sebelum' => 'float',
        'nilai_baru' => 'float',
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

    public function jenisTabungan(): HasOne
    {
        return $this->hasOne(JenisTabunganModels::class, 'p_jenis_tabungan_id', 'p_jenis_tabungan_id');
    }
}
