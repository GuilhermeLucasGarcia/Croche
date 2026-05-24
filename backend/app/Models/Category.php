<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'CATEGORIA';

    const CREATED_AT = 'DT_CRIACAO';
    const UPDATED_AT = 'DT_ALTERACAO';

    protected $fillable = [
        'NOME',
        'DESCRICAO',
        'IMG_URL',
        'CATEGORIA_PAI_ID',
        'ATIVO',
    ];

    protected $casts = [
        'ATIVO' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'CATEGORIA_PAI_ID');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'CATEGORIA_PAI_ID');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'CATEGORIA_ID');
    }
}
