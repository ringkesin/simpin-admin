<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPengajuanModels extends Model
{
    use HasFactory;

    protected $table = 'p_status_pengajuan';

    protected $primaryKey = 'p_status_pengajuan_id';

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
