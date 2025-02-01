<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rbac_menu';

    public $timestamps = false;

    public function apps(): HasOne
    {
        return $this->hasOne(AppsModel::class, 'id', 'apps_id');
    }
}
