<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pessoa extends Authenticatable
{
    use Notifiable;

    protected $table = 'PESSOA';

    const CREATED_AT = null;
    const UPDATED_AT = 'DT_ALTERACAO';

    protected $fillable = [
        'IMG_URL',
        'NOME',
        'NOME_USUARIO',
        'SENHA',
        'PERFIL',
        'EMAIL',
        'RESET_TOKEN_HASH',
        'RESET_TOKEN_EXPIRES',
        'SENHA_ANTERIOR_1',
        'SENHA_ANTERIOR_2',
        'SENHA_ANTERIOR_3',
    ];

    protected $hidden = [
        'SENHA',
        'RESET_TOKEN_HASH',
        'SENHA_ANTERIOR_1',
        'SENHA_ANTERIOR_2',
        'SENHA_ANTERIOR_3',
    ];

    protected $casts = [
        'DT_ALTERACAO' => 'datetime',
        'RESET_TOKEN_EXPIRES' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->SENHA;
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(PessoaNotificationPreference::class, 'pessoa_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(PessoaActivityLog::class, 'pessoa_id');
    }
}
