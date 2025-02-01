<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAccessModel extends Model
{
    use HasFactory;
    use HasUlids;
    // use SoftDeletes;

    protected $table = 'rbac_role_permission';

    public $timestamps = false;

    public function role(): HasOne
    {
        return $this->hasOne(RoleModel::class, 'id', 'role_id');
    }

    public function permission(): HasOne
    {
        return $this->hasOne(PermissionModel::class, 'id', 'permission_id');
    }
}
