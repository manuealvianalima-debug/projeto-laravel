<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PalavraChave extends Model
{
    use HasFactory;

    protected $table = 'palavras_chave';

    protected $fillable = [
        'nome',
        'id_idioma',
    ];

    public function tecnologiasIdiomas(): BelongsToMany
    {
        return $this->belongsToMany(
            Tecnologias_idiomas::class,
            'palavra_chave_tecnologia',
            'palavra_chave_id',
            'tecnologia_id',
            'id',
            'id'
        );
    }
}