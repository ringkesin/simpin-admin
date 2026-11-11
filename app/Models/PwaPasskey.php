<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PwaPasskey extends Model
{
    use HasUlids;

    protected $table = 't_pwa_passkeys';

    protected $guarded = [];

    protected $hidden = ['credential_id', 'credential_id_hash', 'user_handle', 'public_key', 'sign_count'];

    protected function casts(): array
    {
        return ['last_used_at' => 'datetime', 'sign_count' => 'integer'];
    }
}
