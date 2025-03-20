<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitModels extends Model
{
    use HasFactory;

    protected $table = 'p_unit';
    
    protected $fillable = [
        'unit_name',
        'kode_unit',
        'parent_id',
        'location',
        'longitude',
        'latitude',
        'is_project',
        'created_by',
        'updated_by'
    ];
}
