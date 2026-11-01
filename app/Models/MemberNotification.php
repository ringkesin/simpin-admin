<?php

namespace App\Models;

use App\Models\Master\AnggotaModels;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberNotification extends Model
{
    use HasUlids;

    protected $table = 't_notifications';

    protected $fillable = [
        'p_anggota_id',
        'user_id',
        'source',
        'event_id',
        'type',
        'title',
        'body',
        'data',
        'occurred_at',
        'read_at',
        'push_attempted_at',
        'push_accepted_count',
        'push_failed_count',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'occurred_at' => 'datetime',
            'read_at' => 'datetime',
            'push_attempted_at' => 'datetime',
            'push_accepted_count' => 'integer',
            'push_failed_count' => 'integer',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(AnggotaModels::class, 'p_anggota_id', 'p_anggota_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
