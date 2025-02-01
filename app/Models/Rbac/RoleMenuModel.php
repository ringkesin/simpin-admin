<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMenuModel extends Model
{
    use HasFactory;
    use HasUlids;
    // use SoftDeletes;

    protected $table = 'rbac_role_menu';

    public $timestamps = false;

    public function role(): HasOne
    {
        return $this->hasOne(RoleModel::class, 'id', 'role_id');
    }

    public function menu(): HasOne
    {
        return $this->hasOne(MenuModel::class, 'id', 'menu_id');
    }

    public function roleMenuItem(): HasMany
    {
        return $this->hasMany(RoleMenuItemModel::class, 'role_menu_id', 'id');
    }
}
