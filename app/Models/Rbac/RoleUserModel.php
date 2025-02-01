<?php

namespace App\Models\Rbac;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUserModel extends Model
{
    use HasFactory;
    use HasUlids;
    // use SoftDeletes;

    protected $table = 'rbac_role_users';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'role_id',
        'user_id',
        'valid_from',
        'created_by',
        'updated_by'
    ];

    public function role(): HasOne
    {
        return $this->hasOne(RoleModel::class, 'id', 'role_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function roleMenu(): HasMany
    {
        return $this->HasMany(RoleMenuModel::class, 'role_id', 'role_id');
    }
}
