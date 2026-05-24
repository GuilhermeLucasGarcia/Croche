<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $table = 'CARROSSEL';

    protected $fillable = [
        'TITULO',
        'DESCRICAO',
        'IMG_DESKTOP_URL',
        'IMG_MOBILE_URL',
        'LINK_DESTINO',
        'ATIVO',
        'ORDEM',
    ];

    public $timestamps = false;
}
