<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anotacao extends Model
{
    use HasFactory;

    protected $table = 'anotacoes';

    protected $fillable = [
        'tecnologia_id',
        'corpo',
        'autor',
        'gravidade',
        'data',
    ];

    public function tecnologia()
    {
        return $this->belongsTo(Tecnologias_idiomas::class);
    }
}
