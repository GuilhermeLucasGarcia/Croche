<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaActivityLog extends Model
{
    protected $table = 'pessoa_activity_logs';

    protected $fillable = [
        'pessoa_id',
        'action',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
