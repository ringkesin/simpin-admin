<?php

namespace App\Models\Rbac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rbac_role';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function apps(): HasOne
    {
        return $this->hasOne(AppsModel::class, 'id', 'apps_id');
    }

    // public function roleUser(): HasMany
    // {
    //     return $this->hasOne(RoleUserModel::class, 'role_id', 'id');
    // }

    public function roleMenu(): HasMany
    {
        return $this->hasMany(RoleMenuModel::class, 'role_id', 'id');
    }

}
