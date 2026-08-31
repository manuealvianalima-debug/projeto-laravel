<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estagio extends Model
{
    use HasFactory;

    protected $table = 'estagios';

    protected $fillable = [
        'nome',
        'etapa',
        'descricao',
        'id_categoria',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function tecnologias(): HasMany
    {
        return $this->hasMany(Tecnologia::class);
    }
}