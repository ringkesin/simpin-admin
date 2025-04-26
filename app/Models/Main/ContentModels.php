<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Master\ContentTypeModels;

class ContentModels extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 't_content';

    protected $primaryKey = 't_content_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'p_content_type_id',
        'thumbnail_path',
        'content_title',
        'content_text',
        'valid_from',
        'valid_to',
        'created_by',
        'updated_by',
    ];

    // protected function casts(): array
    // {
    //     return [
    //         'valid_from' => 'date',
    //         'valid_to' => 'date',
    //     ];
    // }

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function contentType(): HasOne
    {
        return $this->hasOne(ContentTypeModels::class, 'p_content_type_id', 'p_content_type_id');
    }
}
