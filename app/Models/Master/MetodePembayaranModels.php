<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaranModels extends Model
{
    use SoftDeletes;

    protected $table = 'p_metode_pembayaran';

    protected $primaryKey = 'p_metode_pembayaran_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'metode_code',
        'metode_name',
        'created_at',
        'updated_at'
    ];
}
