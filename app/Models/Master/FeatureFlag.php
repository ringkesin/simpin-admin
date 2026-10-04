<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureFlag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'p_feature_flags';

    protected $fillable = [
        'code',
        'name',
        'is_enabled',
        'description',
        'coming_soon_message',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }
}
