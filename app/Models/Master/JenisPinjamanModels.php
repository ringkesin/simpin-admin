<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPinjamanModels extends Model
{
    use HasFactory;

    protected $table = 'p_jenis_pinjaman';

    protected $primaryKey = 'p_jenis_pinjaman_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nama',
        'created_at',
        'updated_at'
    ];
}
