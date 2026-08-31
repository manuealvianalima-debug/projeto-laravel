<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumeroFiocruz extends Model
{
    protected $table = 'numero_fiocruz';

    protected $fillable = [
        'numero_caso_fiocruz',
    ];
}