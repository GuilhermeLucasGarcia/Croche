<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'PRODUTO';

    const CREATED_AT = null; // Tabela não possui DT_CRIACAO
    const UPDATED_AT = 'DT_ALTERACAO';

    protected $fillable = [
        'CODIGO',
        'DETALHES',
        'DESTAQUE',
        'IMG_URL',
        'DESCRICAO',
        'VALOR',
        'CATEGORIA_ID',
        'MARCA_ID',
    ];

    protected $casts = [
        'DESTAQUE' => 'boolean',
        'VALOR' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'CATEGORIA_ID');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'MARCA_ID');
    }
}
