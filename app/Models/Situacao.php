
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Situacao extends Model
{
    use HasFactory;

    protected $table = 'situacoes';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function tecnologias()
    {
        return $this->hasMany(Tecnologia::class, 'situacao_id');
    }
}