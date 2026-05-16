<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaNotificationPreference extends Model
{
    protected $table = 'pessoa_notification_preferences';

    protected $fillable = [
        'pessoa_id',
        'email_enabled',
        'push_enabled',
        'sms_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
