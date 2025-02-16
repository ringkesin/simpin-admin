<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AppsParamModels extends Model
{
    use HasFactory;
   // use HasRecursiveRelationships;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps_param';

    protected $fillable = [
        'apps_id',
        'param_key',
        'param_value',
        'data_type',
        'remarks',
        'created_by',
        'updated_by'

    ];

    public function namaAplikasi(): HasOne
    {
        return $this->hasOne(AppsModels::class, 'id', 'apps_id');
    }

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
