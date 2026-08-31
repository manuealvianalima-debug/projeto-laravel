<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icone extends Model
{
    use HasFactory;
    
    protected $table = 'icons';
    
    protected $fillable = ['name'];
    
    public function diferenciais()
    {
        // Corrigir: a chave estrangeira é 'icone' na tabela diferenciais, não 'icons'
        return $this->hasMany(Diferencial::class, 'icone', 'name');
    }
}