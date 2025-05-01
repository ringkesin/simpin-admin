<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatReferenceTableModels extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'p_chat_reference_table';
    protected $primaryKey = 'p_chat_reference_table_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'reference_table_name',
        'custom_name',
        'reference_table_key_name',
        'created_at',
        'updated_at'
    ];
}
