<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTypeModels extends Model
{
    use HasFactory;

    protected $table = 'p_content_type';

    protected $primaryKey = 'p_content_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'content_code',
        'content_name',
        'created_at',
        'updated_at'
    ];
}
