<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doenca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'id_idioma',
    ];

    public function tecnologias(): BelongsToMany
    {
        return $this->belongsToMany(Tecnologia::class, 'doenca_tecnologia');
    }
}
