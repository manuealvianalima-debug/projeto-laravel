<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tecnologias_idiomas extends Model
{
    use HasFactory, SoftDeletes;

    // ...

    public function getImagemUrlAttribute(): ?string
    {
        if (empty($this->imagem_lateral)) {
            return null;
        }

        // URL completa
        if (Str::startsWith($this->imagem_lateral, [
            'http://',
            'https://',
        ])) {
            return $this->imagem_lateral;
        }

        // Imagem herdada do Drupal
        if (Str::startsWith($this->imagem_lateral, '/sites/')) {
            return 'https://portfoliodeinovacao.fiocruz.br' .
                $this->imagem_lateral;
        }

        // Imagem enviada pelo Laravel
        return Storage::url($this->imagem_lateral);
    }
}