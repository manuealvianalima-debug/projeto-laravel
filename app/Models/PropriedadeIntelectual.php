<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropriedadeIntelectual extends Model
{
    use HasFactory;

    protected $table = 'propriedades_intelectuais';

    protected $fillable = [
        'tecnologia_id',
        'possui_pi',
        'tipo_propriedade_id',
        'tipo',
        'descricao',
        'link_propriedade',
        'numero_registro',
        'data_registro',
        'link',
    ];
     protected $casts = [
        'data_registro' => 'date',
        'possui_pi' => 'boolean',
    ];
    public function tecnologiaIdioma()
    {
        return $this->belongsTo(Tecnologias_idiomas::class, 'tecnologia_id');
    }
    public function tipoPropriedade()
    {
        return $this->belongsTo(TipoPropriedade::class, 'tipo_propriedade_id');
    }

}
