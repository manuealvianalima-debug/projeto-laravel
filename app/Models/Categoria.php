<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nome',
        'descricao',
        'id_idioma',
    ];

    public function idioma(): BelongsTo
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'id_categoria');
    }

    public function scopePorIdioma($query, $idioma = 'pt-br')
    {
        return $query->where('id_idioma', $idioma === 'pt-br' ? 1 : 2);
    }
}



