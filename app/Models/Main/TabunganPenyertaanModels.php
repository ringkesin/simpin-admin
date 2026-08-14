<?php

namespace App\Models\Main;

use App\Models\Master\AnggotaModels;
use App\Models\Master\JenisTabunganModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TabunganPenyertaanModels extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 't_tabungan_penyertaan';

    protected $primaryKey = 't_tabungan_penyertaan_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'p_jenis_tabungan_id',
        'jumlah',
        'penyertaan_date',
        'status_penyertaan',
        'catatan_user',
        'catatan_approver',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'p_anggota_id' => 'integer',
        'p_jenis_tabungan_id' => 'integer',
        'penyertaan_date' => 'datetime',
        'jumlah' => 'float',
    ];

    public function updatedBy(): HasOne
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

    public function files(): HasMany
    {
        return $this->hasMany(TabunganPenyertaanFileModels::class, 't_tabungan_penyertaan_id', 't_tabungan_penyertaan_id');
    }
}
