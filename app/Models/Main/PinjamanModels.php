<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\AnggotaModels;
use App\Models\Master\JenisPinjamanModels;
use App\Models\Master\StatusPengajuanModels;

class PinjamanModels extends Model
{
    use HasFactory;

    protected $table = 't_pinjaman';

    protected $primaryKey = 't_pinjaman_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_anggota_id',
        'p_jenis_pinjaman_id',
        'ra_jumlah_pinjaman',
        'ri_jumlah_pinjaman',
        'alamat',
        'prakiraan_nilai_pasar',
        'no_rekening',
        'bank',
        'doc_ktp',
        'doc_surat_nikah',
        'doc_kk',
        'doc_kartu_anggota',
        'doc_slip_gaji',
        'p_status_pengajuan_id',
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

    public function masterJenisPinjaman(): HasOne
    {
        return $this->hasOne(JenisPinjamanModels::class, 'p_jenis_pinjaman_id', 'p_jenis_pinjaman_id');
    }

    public function masterStatusPengajuan(): HasOne
    {
        return $this->hasOne(StatusPengajuanModels::class, 'p_status_pengajuan_id', 'p_status_pengajuan_id');
    }
}
