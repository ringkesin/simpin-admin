<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Master\JenisPinjamanModels;

class SimulasiPinjamanModels extends Model
{
    use HasFactory;

    protected $table = 't_simulasi_pinjaman';

   // protected $primaryKey = 't_simulasi_pinjaman';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_jenis_pinjaman_id',
        'tenor',
        'margin',
        'tahun_margin',
        'status'
    ];

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function jenisPinjaman(): HasOne
    {
        return $this->hasOne(JenisPinjamanModels::class, 'p_jenis_pinjaman_id', 'p_jenis_pinjaman_id');
    }
}
