<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Papel;
use App\Models\Unidade;
use App\Models\Tecnologia;

#[Fillable(['name', 'email', 'password', 'is_admin', 'ativo', 'ultimo_acesso', 'descricao'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function papel()
    {
        return $this->belongsTo(Papel::class);
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function tecnologias()
    {
        return $this->hasMany(Tecnologia::class, 'id_user_criador');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'ativo' => 'boolean',
        'ultimo_acesso' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        if ($this->email === 'manuela.viana@fiocruz.br') {
            return true;
        }

        return (bool) ($this->attributes['is_admin'] ?? false);
    }

    public function getIsAdminAttribute($value)
    {
        if ($this->email === 'manuela.viana@fiocruz.br') {
            return true;
        }

        return (bool) $value;
    }
}
