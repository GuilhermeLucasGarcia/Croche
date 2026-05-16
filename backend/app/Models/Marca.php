<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'MARCA';

    const CREATED_AT = null;
    const UPDATED_AT = 'DT_ALTERACAO';

    protected $fillable = [
        'NOME',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'MARCA_ID');
    }
}
