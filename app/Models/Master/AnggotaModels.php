<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnggotaModels extends Model
{
    use HasFactory;

    protected $table = 'p_anggota';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'tanggal_masuk',
        'ktp',
        'tempat_lahir',
        'tgl_lahir',
        'p_jenis_kelamin_id',
        'user_id',
        'valid_from',
        'valid_to',
        'created_by',
        'updated_by'
    ];

    // protected $hidden = [
    //     'user_id'
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
}
