<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Papel extends Model
{
    use HasFactory;

    protected $table = 'papeis';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
