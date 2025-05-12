<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPembayaranModels extends Model
{
    use SoftDeletes;

    protected $table = 'p_status_pembayaran';

    protected $primaryKey = 'p_status_pembayaran_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'status_code',
        'status_name',
        'created_at',
        'updated_at'
    ];
}
