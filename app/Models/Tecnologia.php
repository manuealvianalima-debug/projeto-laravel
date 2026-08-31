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
use App\Models\Midia;
use App\Models\PropriedadeIntelectual;
use App\Models\Inventor;
use App\Models\Anotacao;
use App\Models\PalavraChave;
use App\Models\Doenca;
use App\Models\Categoria;
use App\Models\Diferencial;
use App\Models\Tecnologia;
use App\Models\Idioma;

class Tecnologia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tecnologias_idiomas';

    protected $fillable = [
        'titulo',
        'nome',
        'idioma',          // ← Mantém o campo texto (pt-br, en, etc.)
        'id_status',
        'unidade_id',
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
       // 'drupal_nid',
        'drupal_uuid',
        'numero_caso_fiocruz'
    ];

    protected $casts = [
        'data_submissao' => 'date',
        'possui_pi' => 'boolean',
       // 'drupal_nid' => 'integer',
        'tecnologia_id' => 'integer',
    ];

    // ============ MAPEAMENTO DE IDIOMAS ============
    
    /**
     * Mapeia o idioma da tecnologia para o nome do idioma no banco
     */
    private static function mapearIdiomaParaBanco(string $idioma): string
    {
        $mapa = [
            'pt-br' => 'pt_BR',
            'en' => 'en_US',
            'es' => 'es_ES',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'de' => 'de_DE',
        ];
        
        return $mapa[$idioma] ?? 'pt_BR';
    }

    /**
     * Mapeia o idioma do banco para o formato da tecnologia
     */
    private static function mapearIdiomaParaTecnologia(string $nomeBanco): string
    {
        $mapa = [
            'pt_BR' => 'pt-br',
            'en_US' => 'en',
            'es_ES' => 'es',
            'fr_FR' => 'fr',
            'it_IT' => 'it',
            'de_DE' => 'de',
        ];
        
        return $mapa[$nomeBanco] ?? $nomeBanco;
    }

    // ============ RELACIONAMENTO VIRTUAL COM IDIOMAS ============
    
    /**
     * Relacionamento virtual com a tabela idiomas
     * Usa o campo 'idioma' (pt-br, en, etc.) para buscar o idioma correspondente
     * SEM precisar da coluna idioma_id no banco
     */
    public function idiomaRelacionado()
    {
        // Mapeia o idioma da tecnologia para o nome do idioma no banco
        $nomeBanco = $this->mapearIdiomaParaBanco($this->idioma ?? 'pt-br');
        
        // Busca o idioma no banco
        return Idioma::where('nome', $nomeBanco)->first();
    }

    /**
     * Accessor: pega a descrição do idioma
     */
    public function getDescricaoIdiomaAttribute()
    {
        $idioma = $this->idiomaRelacionado();
        return $idioma ? $idioma->descricao : $this->idioma;
    }

    /**
     * Accessor: pega o nome do idioma formatado
     */
    public function getNomeIdiomaFormatadoAttribute()
    {
        $idioma = $this->idiomaRelacionado();
        return $idioma ? $idioma->nome : $this->idioma;
    }

    /**
     * Accessor: pega o ID do idioma
     */
    public function getIdiomaIdAttribute()
    {
        $idioma = $this->idiomaRelacionado();
        return $idioma ? $idioma->id : null;
    }

    // ============ RELACIONAMENTOS EXISTENTES ============

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

    public function midias()
    {
        return $this->hasMany(Midia::class);
    }

    public function propriedadeIntelectual()
    {
        return $this->hasOne(PropriedadeIntelectual::class, 'tecnologia_idioma_id');
    }

    public function propriedadesIntelectuais()
    {
        return $this->hasMany(PropriedadeIntelectual::class, 'tecnologia_idioma_id');
    }

    public function inventores()
    {
        return $this->hasMany(Inventor::class, 'tecnologia_id', 'tecnologias_idiomas_id');
    }

    public function anotacao()
    {
        return $this->hasOne(Anotacao::class);
    }

    // ============ RELACIONAMENTOS MANY-TO-MANY ============

    public function palavrasChave(): BelongsToMany
    {
        return $this->belongsToMany(PalavraChave::class, 'palavra_chave_tecnologia');
    }

    public function doencas(): BelongsToMany
    {
        return $this->belongsToMany(Doenca::class, 'doenca_tecnologia');
    }

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_tecnologia', 'tecnologia_id', 'categoria_id')
                    ->withPivot('estagio_id')
                    ->withTimestamps();
    }

    public function diferenciais(): BelongsToMany
    {
        return $this->belongsToMany(Diferencial::class, 'diferencial_tecnologia');
    }

    // ============ MÉTODOS AUXILIARES ============

    /**
     * Verifica se a tecnologia tem tradução em outro idioma
     */
    public function hasTraducao(string $idioma): bool
    {
        return self::where('tecnologia_id', $this->tecnologia_id)
                   ->where('idioma', $idioma)
                   ->where('id', '!=', $this->id)
                   ->exists();
    }

    /**
     * Busca tradução em idioma específico
     */
    public function getTraducao(string $idioma)
    {
        return self::where('tecnologia_id', $this->tecnologia_id)
                   ->where('idioma', $idioma)
                   ->first();
    }

    /**
     * Busca todas as traduções da mesma tecnologia
     */
    public function getTraducoes()
    {
        return self::where('tecnologia_id', $this->tecnologia_id)
                   ->where('id', '!=', $this->id)
                   ->get();
    }

    /**
     * Verifica se é a versão padrão (português)
     */
    public function isDefault(): bool
    {
        return $this->idioma === 'pt-br';
    }

    /**
     * Busca a lista de todos os idiomas disponíveis com suas descrições
     */
    public static function getListaIdiomas(): array
    {
        return [
            'pt-br' => 'Português - Brasil',
            'en' => 'Inglês - Estados Unidos',
            'es' => 'Espanhol - Espanha',
            'fr' => 'Francês - França',
            'it' => 'Italiano - Itália',
            'de' => 'Alemão - Alemanha',
        ];
    }

    /**
     * Busca a lista de idiomas já traduzidos para uma tecnologia
     */
    public function getIdiomasTraduzidos(): array
    {
        return self::where('tecnologia_id', $this->tecnologia_id)
                   ->pluck('idioma')
                   ->toArray();
    }

    /**
     * Busca a lista de idiomas faltantes para uma tecnologia
     */
    public function getIdiomasFaltantes(): array
    {
        $todos = array_keys(self::getListaIdiomas());
        $existentes = $this->getIdiomasTraduzidos();
        
        return array_diff($todos, $existentes);
    }
}