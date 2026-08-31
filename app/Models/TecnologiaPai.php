<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TecnologiaPai extends Model
{
    protected $table = 'tecnologias';

    protected $fillable = [
        'titulo',
        'nid',
        'numero_caso_fiocruz',
    ];

    public function traducoes(): HasMany
    {
        return $this->hasMany(Tecnologias_idiomas::class, 'tecnologia_id');
    }
}
