<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMenuItemModel extends Model
{
    use HasFactory;
    use HasUlids;
    // use SoftDeletes;

    protected $table = 'rbac_role_menu_item';

    public $timestamps = false;

    public function roleMenu(): HasOne
    {
        return $this->hasOne(RoleMenuModel::class, 'id', 'role_menu_id');
    }

    public function menuItem(): HasOne
    {
        return $this->hasOne(MenuItemsModel::class, 'id', 'menu_item_id')->orderBy('order_no', 'ASC');
    }
}
