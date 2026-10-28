<?php

namespace App\Models;

use App\Models\Master\AnggotaModels;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushDevice extends Model
{
    use HasUlids;

    protected $table = 't_push_devices';

    protected $fillable = [
        'p_anggota_id',
        'client_installation_id',
        'provider',
        'platform',
        'provider_identifier',
        'provider_identifier_hash',
        'app_version',
        'is_active',
        'last_seen_at',
        'disabled_at',
        'last_error',
    ];

    protected $hidden = [
        'provider_identifier',
        'provider_identifier_hash',
    ];

    protected function casts(): array
    {
        return [
            'provider_identifier' => 'encrypted',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'disabled_at' => 'datetime',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(AnggotaModels::class, 'p_anggota_id', 'p_anggota_id');
    }
}
