<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diferencial extends Model
{
    use HasFactory;
    
    protected $table = 'diferenciais';
    
    protected $fillable = [
        'nome',
        'icone',
        'id_idioma',
        'tipo', // 'padrao' ou 'personalizado'
    ];
    
    protected $casts = [
        'icone' => 'string',
        'tipo' => 'string',
    ];
    
    /**
     * Relacionamento com idioma
     */
    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }
    
    /**
     * Relacionamento com tecnologias (muitos para muitos)
     */
    public function tecnologias()
    {
        return $this->belongsToMany(Tecnologias_idiomas::class, 'diferencial_tecnologia', 'diferencial_id', 'tecnologia_id');
    }
}