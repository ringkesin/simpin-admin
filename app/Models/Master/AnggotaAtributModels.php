<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\AnggotaModels;

class AnggotaAtributModels extends Model
{
    use HasFactory;

    protected $table = 'p_anggota_atribut';
    protected $primaryKey = 'p_anggota_atribut_id';

    protected $fillable = [
        'p_anggota_id',
        'atribut_kode',
        'atribut_value',
        'atribut_attachment',
        'created_by',
        'updated_by'
    ];

    protected $appends = ['atribut_kode_beautify'];

    public function masterAnggota(): HasOne
    {
        return $this->hasOne(AnggotaModels::class, 'p_anggota_id', 'p_anggota_id');
    }

    public function getAtributKodeBeautifyAttribute()
    {
        return match ($this->atribut_kode) {
            'ktp' => 'KTP',
            'kartu_pegawai' => 'Kartu Pegawai',
            default => ucwords(str_replace('_', ' ', $this->atribut_kode)),
        };
    }
}
