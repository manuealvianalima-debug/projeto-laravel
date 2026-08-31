<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idiomas';

    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
    ];

    public static function nomeParaSigla(?string $nome): string
    {
        $valor = trim((string) ($nome ?? ''));

        if (preg_match('/^\d+$/', $valor)) {
            return match ((int) $valor) {
                1 => 'pt-br',
                2 => 'en',
                3 => 'es',
                default => 'pt-br',
            };
        }

        $normalizado = Str::ascii(strtolower($valor));

        return match ($normalizado) {
            'portugues', 'português', 'pt', 'pt-br', 'pt_br', 'pt-brasil' => 'pt-br',
            'ingles', 'inglês', 'en', 'en-us', 'en_us' => 'en',
            'espanhol', 'es', 'es-es', 'es_es' => 'es',
            default => $normalizado !== '' ? $normalizado : 'pt-br',
        };
    }

    public static function siglaParaNome(?string $sigla): string
    {
        $valor = trim((string) ($sigla ?? ''));

        if (preg_match('/^\d+$/', $valor)) {
            return match ((int) $valor) {
                1 => 'Português',
                2 => 'Inglês',
                3 => 'Espanhol',
                default => 'Português',
            };
        }

        $normalizado = Str::ascii(strtolower($valor));

        return match ($normalizado) {
            'pt', 'pt-br', 'pt_br', 'pt-brasil' => 'Português',
            'en', 'en-us', 'en_us' => 'Inglês',
            'es', 'es-es', 'es_es' => 'Espanhol',
            default => ucfirst($normalizado !== '' ? $normalizado : 'portugues'),
        };
    }

    public static function getLocaleBySigla(?string $sigla): string
    {
        $siglaNormalizada = self::nomeParaSigla($sigla);

        return match ($siglaNormalizada) {
            'en' => 'en',
            default => 'pt_br',
        };
    }
    // ============ RELACIONAMENTOS ============

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class, 'id_idioma');
    }

    public function estagios(): HasMany
    {
        return $this->hasMany(Estagio::class, 'id_idioma');
    }

    public function diferenciais(): HasMany
    {
        return $this->hasMany(Diferencial::class, 'id_idioma');
    }

    public function doencas(): HasMany
    {
        return $this->hasMany(Doenca::class, 'id_idioma');
    }

    public function palavrasChave(): HasMany
    {
        return $this->hasMany(PalavraChave::class, 'id_idioma');
    }

    public function tiposPropriedade(): HasMany
    {
        return $this->hasMany(TipoPropriedade::class, 'id_idioma');
    }

    public function tecnologias(): HasMany
    {
        return $this->hasMany(Tecnologias_idiomas::class, 'idioma', 'sigla');
    }

    // ============ MÉTODOS AUXILIARES ============
public function idiomaModel()
{
    return $this->belongsTo(Idioma::class, 'idioma');
}
    /**
     * Busca o ID do idioma pela sigla
     */
    public static function getIdBySigla(string $sigla): ?int
    {
        $valor = trim((string) $sigla);
        $nome = self::siglaParaNome($valor);

        return self::where('nome', $nome)->value('id')
            ?? self::where('id', (int) $valor)->value('id');
    }

    /**
     * Busca a sigla pelo ID
     */
    public static function getSiglaById(int $id): ?string
    {
        $nome = self::find($id)?->nome;

        return $nome ? self::nomeParaSigla($nome) : null;
    }

    /**
     * Retorna todos os idiomas como array para selects
     */
    public static function getForSelect(): array
    {
        return self::orderBy('nome')
            ->get()
            ->mapWithKeys(fn ($idioma) => [self::nomeParaSigla($idioma->nome) => $idioma->nome])
            ->toArray();
    }

    /**
     * Retorna todas as siglas disponíveis
     */
    public static function getAllSiglas(): array
    {
        return self::orderBy('id')
            ->get()
            ->map(fn ($idioma) => self::nomeParaSigla($idioma->nome))
            ->toArray();
    }
}