<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Models\Master\AnggotaModels;
use App\Models\Master\StatusPembayaranModels;
use App\Models\Master\MetodePembayaranModels;

class TagihanModels extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 't_tagihan';

    protected $primaryKey = 't_tagihan_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        't_pinjaman_id',
        'uraian',
        'jumlah_tagihan',
        'remarks',
        'bulan',
        'tahun',
        'tgl_jatuh_tempo',
        'p_status_pembayaran_id',
        'paid_at',
        'jumlah_pembayaran',
        'p_metode_pembayaran_id',
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

    public function pinjamanAnggota(): HasOne
    {
        return $this->hasOne(PinjamanModels::class, 't_pinjaman_id', 't_pinjaman_id');
    }

    public function metodePembayaran(): HasOne
    {
        return $this->hasOne(MetodePembayaranModels::class, 'p_metode_pembayaran_id', 'p_metode_pembayaran_id');
    }

    public function statusPembayaran(): HasOne
    {
        return $this->hasOne(StatusPembayaranModels::class, 'p_status_pembayaran_id', 'p_status_pembayaran_id');
    }

}
