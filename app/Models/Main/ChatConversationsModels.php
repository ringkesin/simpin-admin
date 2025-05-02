<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Main\ChatModels;

class ChatConversationsModels extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 't_chat_conversations';
    protected $primaryKey = 't_chat_conversations_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        't_chat_id',
        'message_text',
        'is_read_user',
        'is_read_admin',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function updatedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function chat(): HasOne
    {
        return $this->hasOne(ChatModels::class, 't_chat_id', 't_chat_id');
    }
}
