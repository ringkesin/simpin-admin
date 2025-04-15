<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PinjamanKeperluanModels extends Model
{
    use SoftDeletes;
    
    protected $table = 'p_pinjaman_keperluan';

    protected $primaryKey = 'p_pinjaman_keperluan_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'keperluan',
        'created_at',
        'updated_at'
    ];
}
