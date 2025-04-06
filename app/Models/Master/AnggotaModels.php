<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\AnggotaAtributModels;
use App\Models\Master\UnitModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnggotaModels extends Model
{
    use HasFactory;

    protected $table = 'p_anggota';

    protected $primaryKey = 'p_anggota_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nomor_anggota',
        'nama',
        'nik',
        'alamat',
        'tanggal_masuk',
        'ktp',
        'tempat_lahir',
        'tgl_lahir',
        'p_jenis_kelamin_id',
        'user_id',
        'email',
        'mobile',
        'is_registered',
        'valid_from',
        'valid_to',
        'created_by',
        'updated_by',
        'p_unit_id'
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_to' => 'date',
        ];
    }

    // protected $hidden = [
    //     'p_anggota_id'
    // ];

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function userId(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function atribut(): HasMany
    {
        return $this->hasMany(AnggotaAtributModels::class, 'p_anggota_id', 'p_anggota_id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(UnitModels::class, 'id', 'p_unit_id');
    }
}
