<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Unidade;
use App\Models\Situacao;
use App\Models\Estagio;
use App\Models\PropriedadeIntelectual;
use App\Models\Inventor;
use App\Models\Anotacao;
use App\Models\PalavraChave;
use App\Models\Doenca;
use App\Models\Categoria;
use App\Models\Diferencial;
use App\Models\TecnologiaPai;
use App\Models\Idioma;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Tecnologias_idiomas extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tecnologias_idiomas';

    protected $fillable = [
        'titulo',
        'nome',
        'idioma',
        'id_status',
        'unidade_id',
        'numero_caso',
        'tecnologia_id',
        'data_submissao',
        'resumo_solucao',
        'problema',
        'solucao',
        'o_que_buscam',
        'estagio_id',
        'situacao_id',
        'id_user_criador',
        'possui_pi',
        'imagem_lateral',
        'url_youtube',
        'descricao_imagem_video',
        'slug',
        'numero_caso_fiocruz',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'data_submissao' => 'date',
        'possui_pi' => 'boolean',
        'tecnologia_id' => 'integer',
        'id_idioma' => 'integer'

    ];

    // ============ RELACIONAMENTOS ============
     public function tecnologia()
    {
        return $this->belongsTo(TecnologiaPai::class, 'tecnologia_id');
    }
    public function responsavel()
    {
        return $this->belongsTo(User::class, 'id_user_criador');
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unidade_id');
    }

    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao_id');
    }

    public function status()
    {
        return $this->belongsTo(Situacao::class, 'id_status');
    }

    public function estagio()
    {
        return $this->belongsTo(Estagio::class, 'estagio_id');
    }

/*
|--------------------------------------------------------------------------
| ACCESSOR PARA IMAGEM
|--------------------------------------------------------------------------
*/
public function getImagemUrlAttribute(): ?string
{
    if (empty($this->imagem_lateral)) {
        return null;
    }

    // URL complet*
    if (Str::startsWith($this->imagem_lateral, [
        'http://',
        'https://',
    ])) {
       return $this->imagem_lateral;
   }

    // Imagem legada do Drupal    
    if (Str::startsWith($this->imagem_lateral, [
        '/sites/',
        '/files/',
    ])) {
       return 'https://portfoliodeinovacao.fiocruz.br' . $this->imagem_lateral;
    }
    // Imagem armazenada*pelo Laravel
    return Storage::url($this->imagem_lateral);
}
//////////////////////////////////////////////////////////
/* Video link youtube */
public function getVideoEmbedUrlAttribute(): ?string
{
    if (empty($this->url_youtube)) {
        return null;
    }

    $url = trim($this->url_youtube);

    // Se já for uma URL de embed
    if (str_contains($url, 'youtube.com/embed/')) {
        return $url;
    }

    $videoId = null;

    // URL padrão:
    // https://www.youtube.com/watch?v=WhsHZdryidc
    if (str_contains($url, 'youtube.com/watch')) {
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            parse_str($query, $params);
            $videoId = $params['v'] ?? null;
        }
    }

    // URL curta:
    // https://youtu.be/WhsHZdryidc
    if (!$videoId && str_contains($url, 'youtu.be/')) {
        $path = parse_url($url, PHP_URL_PATH);
        $videoId = trim($path, '/');
    }

    if (!$videoId) {
        return null;
    }

    return 'https://www.youtube.com/embed/' . $videoId;
}

    public function inventores()
    {
        return $this->hasMany(
            Inventor::class,
        'tecnologia_id', // campo da tabela inventores

        'tecnologia_id' // campo da tabela tecnologias_idiomas
        );
    }
    public function anotacao()
    {
        return $this->hasOne(Anotacao::class);
    }

    // ============ RELACIONAMENTOS MANY-TO-MANY ============

public function palavrasChave(): BelongsToMany
{
    return $this->belongsToMany(
        PalavraChave::class,
        'palavra_chave_tecnologia',
        'tecnologia_id',
        'palavra_chave_id',
        'tecnologia_id',
        'id'
    );
}

public function doencas(): BelongsToMany
{
    return $this->belongsToMany(
        Doenca::class, 
        'doenca_tecnologia',
        'tecnologia_id',        // corrected
        'doenca_id',
        'tecnologia_id'
    );
}

public function categorias(): BelongsToMany
{
    return $this->belongsToMany(
        Categoria::class, 
        'categoria_tecnologia',
        'tecnologia_id',
        'categoria_id',
        'tecnologia_id'
    )
    ->withPivot('estagio_id')
    ->withTimestamps();
}

public function diferenciais(): BelongsToMany
{
    return $this->belongsToMany(
        Diferencial::class, 
        'diferencial_tecnologia',
        'tecnologia_id',        // corrected
        'diferencial_id',
        'tecnologia_id'
    );
}
    
    // ============ METODOS AUXILIARES ============

    /**
     * Verifica se a tecnologia tem propriedade intelectual
     */
    public function propriedadesIntelectuais()
{
    return $this->hasMany(
        PropriedadeIntelectual::class,
        'tecnologia_id',
        'tecnologia_id'
    );
}

    public function hasPropriedadeIntelectual(): bool
    {
        return $this->propriedadesIntelectuais()->exists();
    }

    /**
     * Busca propriedades intelectuais ativas
     */
    public function getPropriedadesAtivas()
    {
        return $this->propriedadesIntelectuais()
                    ->where('possui_propriedade', true)
                    ->get();
    }
}
