<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TabunganPenyertaanFileModels extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $table = 't_tabungan_penyertaan_file';

    protected $primaryKey = 't_tabungan_penyertaan_file_id';

    protected $fillable = [
        't_tabungan_penyertaan_id',
        'nama_file',
        'path_file',
        'mime_type',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function penyertaan(): BelongsTo
    {
        return $this->belongsTo(TabunganPenyertaanModels::class, 't_tabungan_penyertaan_id', 't_tabungan_penyertaan_id');
    }
}
