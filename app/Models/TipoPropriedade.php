<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPropriedade extends Model
{
    use HasFactory;

    protected $table = 'tipo_propriedade';

    protected $fillable = ['nome', 'descricao', 'id_idioma'];

    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }
}
