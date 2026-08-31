<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventor extends Model
{
    use HasFactory;

    protected $table = 'inventores';

    protected $fillable = [
        'tecnologias_idiomas_id',  // ← mantenha esse nome
        'tecnologia_id',            // ← mantenha esse também
        'nome',
        'linkedin',
        'lattes',
        'coordenador',
        'email',
        'instituicao'
    ];

    // Relacionamento com Tecnologias_idiomas (via tecnologias_idiomas_id)
    public function tecnologiaIdioma()
    {
        return $this->belongsTo(Tecnologias_idiomas::class, 'tecnologias_idiomas_id');
    }

    // Relacionamento com Tecnologia (via tecnologia_id)
    public function tecnologia()
    {
        return $this->belongsTo(Tecnologia::class, 'tecnologia_id');
    }
}