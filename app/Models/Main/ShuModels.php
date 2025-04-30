<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\AnggotaModels;
use Livewire\WithPagination;

class ShuModels extends Model
{
    use HasFactory;
    use WithPagination;

    protected $table = 't_shu';

    protected $primaryKey = 't_shu_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'shu_diterima',
        'shu_dibagi',
        'shu_ditabung',
        'shu_tahun_lalu',
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
