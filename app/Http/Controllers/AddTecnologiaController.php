<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Diferencial;
use App\Models\Doenca;
use App\Models\Estagio;
use App\Models\PalavraChave;
use App\Models\Situacao;
use App\Models\TipoPropriedade;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Icone;
use App\Models\PropriedadeIntelectual;
use App\Models\Tecnologias_idiomas;
use App\Models\Tecnologia;
use App\Models\TecnologiaPai;
use App\Support\TecnologiaActivity;
use App\Models\Idioma;

class AddTecnologiaController extends Controller
{
    public function index(Request $request)
{
    $idiomaSelecionado = $request->filled('origem')
        ? (string) ($request->query('idioma') ?? 'pt-br')
        : 'pt-br';

    app()->setLocale(Idioma::getLocaleBySigla($idiomaSelecionado));

    $idiomaPadrao = 'pt-br';
    $idiomaCriacao = in_array($idiomaSelecionado, ['pt-br', 'pt_br', 'pt'], true)
        ? $idiomaPadrao
        : $idiomaSelecionado;

    $todosIdiomas = Idioma::orderBy('id')->get();

    // Tecnologia de origem (para criar versão em outro idioma)
    $tecnologiaOrigem = null;
    if ($request->filled('origem')) {
        $tecnologiaOrigem = Tecnologias_idiomas::findOrFail($request->origem);
    }

    // Busca unidades
    $unidades = Unidade::orderBy('nome')->get();
    $idiomaId = Idioma::getIdBySigla($idiomaCriacao) ?? 1;

    //Busca diferenciais por idioma
    $diferenciais = Diferencial::where('id_idioma', $idiomaId)
        ->orderBy('nome')
        ->get();

    // Busca tipos de propriedade por idioma
    $tipos_propriedade = TipoPropriedade::where('id_idioma', $idiomaId)
        ->orderBy('nome')
        ->get();

    // Busca os ícones (para o select de ícones personalizados)
    $icones = Icone::orderBy('name')->pluck('name')->toArray();

    // Filtra categorias por idioma

    //$categorias = Categoria::where('id_idioma',  $idiomaId)->get();
    $categorias = Categoria::where('id_idioma', $idiomaId)
        ->orWhereNull('id_idioma')
        ->orderBy('nome')
        ->get();
    $estagiosPorCategoria = $this->estagiosPorCategoria($idiomaId, $categorias);

    $doencas = Doenca::where('id_idioma', $idiomaId)
        ->orderBy('nome')
        ->get();
        
    $palavras_chave = PalavraChave::where('id_idioma', $idiomaId) 
        ->orderBy('nome')
        ->get();
        

    return view('technology.index', compact(
        'tecnologiaOrigem',
        'unidades',
        'categorias',
        'estagiosPorCategoria',
        'doencas',
        'diferenciais',
        'palavras_chave',
        'tipos_propriedade',
        'idiomaId',
        'icones',
        'todosIdiomas',
        'idiomaCriacao'
    ));
} 
    public function show(Tecnologias_idiomas $tecnologia)
    {
        app()->setLocale(
            Idioma::getLocaleBySigla($tecnologia->idioma ?? 'pt-br')
        );
        $todasVersoes = Tecnologias_idiomas::where('numero_caso_fiocruz', $tecnologia->numero_caso_fiocruz);

        $tecnologia->load([
            'tecnologia',
            'situacao',
            'estagio',
            'unidade',
            'inventores',
            'categorias',
            'diferenciais',
            'doencas',
            'palavrasChave',
            'propriedadesIntelectuais',
            'propriedadesIntelectuais.tipoPropriedade',
        ]);
        
        // ✅ Buscar todas as versões da mesma tecnologia. O vínculo pai é
        // mais confiável para rascunhos do que depender apenas do número do caso.
        $versoesQuery = Tecnologias_idiomas::query();
        if ($tecnologia->tecnologia_id) {
            $versoesQuery->where('tecnologia_id', $tecnologia->tecnologia_id);
        } else {
            $versoesQuery->where('numero_caso_fiocruz', $tecnologia->numero_caso_fiocruz);
        }

        $todasVersoes = $versoesQuery
            ->with(['situacao', 'unidade'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(fn ($versao) => Idioma::nomeParaSigla((string) $versao->idioma));

        // ✅ Versões para exibir (excluindo a atual)
        $versoes = $todasVersoes->where('id', '!=', $tecnologia->id)->keyBy('idioma');

        // ✅ Idiomas que já têm versão, sempre comparados pelas siglas normalizadas.
        $idiomasExistentes = $todasVersoes
            ->pluck('idioma')
            ->map(fn ($idioma) => Idioma::nomeParaSigla((string) $idioma))
            ->unique()
            ->values()
            ->all();

        // ✅ Todos os idiomas disponíveis (mapeados para o formato usado nas tecnologias)
        $todosIdiomas = Idioma::orderBy('nome')->get()->map(function ($idioma) {
            return (object) [
                'id' => $idioma->id,
                'nome' => Idioma::nomeParaSigla($idioma->nome),
                'descricao' => $idioma->descricao,
                'nome_original' => $idioma->nome,
            ];
        });
        
        // ✅ Idiomas que NÃO têm versão ainda (para criar)
        $idiomasFaltantes = $todosIdiomas->filter(function ($idioma) use ($idiomasExistentes) {
            return ! in_array($idioma->nome, $idiomasExistentes, true);
        });

        return view('technology.show', compact('tecnologia', 'versoes', 'idiomasFaltantes', 'todasVersoes'));
    }

    /**
     * Abre o cadastro em inglês para uma tecnologia existente.
     */
    public function versaoIngles(Tecnologias_idiomas $tecnologia)
{
    // ✅ CORRETO: Buscar versão em inglês pelo tecnologia_id
    $versaoIngles = Tecnologias_idiomas::query()
        ->where('tecnologia_id', $tecnologia->tecnologia_id)
        ->whereIn('idioma', ['en', 'en_US'])
        ->first();

    if ($versaoIngles) {
        return redirect()->route('technology.show', $versaoIngles);
    }

    return redirect()
        ->route('technology.index', ['idioma' => 'en', 'origem' => $tecnologia->id])
        ->with('info', 'Preencha os dados para cadastrar a versão em inglês.');
}

    public function edit(Request $request, Tecnologias_idiomas $tecnologia)
            {
        $idiomaSelecionado = (string) ($request->query('idioma') ?: $tecnologia->idioma ?: 'pt-br');
        $locale = Idioma::getLocaleBySigla($idiomaSelecionado);

        app()->setLocale($locale);

        $idiomaAtual = Idioma::nomeParaSigla($idiomaSelecionado);


        $tecnologia->load([
            'tecnologia',
            'situacao',
            'estagio',
            'unidade',
            'categorias',
            'diferenciais',
            'doencas',
            'palavrasChave',
            'inventores',
            'propriedadesIntelectuais', 
        ]);
        
        $selectedDiferenciais = $tecnologia->diferenciais->map(fn($d) => [
            'id' => $d->id,
            'nome' => $d->nome,
            'icone' => $d->icone,
            'tipo' => $d->tipo,
        ])->toArray();
        
        // Busca os estágios agrupados por id_categoria
        $estagiosAgrupados = Estagio::whereNotNull('id_categoria')
            ->orderBy('id_categoria')
            ->orderBy('nome')
            ->get()
            ->groupBy('id_categoria')
            ->map(function ($group) {
                return $group->map(fn($e) => ['id' => $e->id, 'nome' => $e->nome])->values()->all();
            })
            ->toArray();

        $situacoes = Situacao::orderBy('nome')->get();
        $unidades = Unidade::orderBy('nome')->get();
        $idiomaId = Idioma::getIdBySigla($idiomaAtual) ?? Idioma::getIdBySigla((string) ($tecnologia->idioma ?? 'pt-br')) ?? 1;

        $categorias = Categoria::where('id_idioma', $idiomaId)
            ->orWhereNull('id_idioma')
            ->orderBy('nome')
            ->get();

        $selectedCategories = $tecnologia->categorias->pluck('id')->toArray();

        $selectedEstagio = $tecnologia->estagio_id;

        $estagiosPorCategoria = $this->estagiosPorCategoria($idiomaId, $categorias);
        
        $diferenciais = Diferencial::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();
            
        $tipos_propriedade = TipoPropriedade::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();

        // Busca os ícones (para o select de ícones personalizados)
        $icones = Icone::orderBy('name')->pluck('name')->toArray();

        // Busca doenças e palavras-chave
        $doencas = Doenca::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();
        $palavras_chave = PalavraChave::where('id_idioma', $idiomaId)
            ->orderBy('nome')
            ->get();

        if ($situacoes->isEmpty()) {
            $situacoes = collect([
                Situacao::firstOrCreate(['nome' => 'Rascunho']),
                Situacao::firstOrCreate(['nome' => 'Em Análise']),
                Situacao::firstOrCreate(['nome' => 'Em Desenvolvimento']),
                Situacao::firstOrCreate(['nome' => 'Concluída']),
            ]);
        }

        // ✅ Buscar todos os idiomas para o select
        $todosIdiomas = Idioma::orderBy('nome')->get()->map(function ($idioma) {
            return (object) [
                'id' => $idioma->id,
                'nome' => Idioma::nomeParaSigla($idioma->nome),
                'descricao' => $idioma->descricao,
                'nome_original' => $idioma->nome,
            ];
        });

        return view('technology.edit', compact(
            'tecnologia',
            'situacoes',
            'unidades',
            'categorias',
            'diferenciais',
            'tipos_propriedade',
            'estagiosPorCategoria',
            'idiomaId',
            'icones',
            'doencas',
            'palavras_chave',
            'selectedDiferenciais',
            'todosIdiomas',
            'selectedCategories',
            'selectedEstagio',
        ));
    }

public function store(Request $request)
{
    
    // ✅ Lista de idiomas disponíveis (apenas os 6 definidos)
    //$idiomasDisponiveis = ['pt-br', 'en', 'es', 'fr', 'it', 'de'];
    
    $idioma = Idioma::getIdBySigla(
        Idioma::nomeParaSigla((string) ($request->input('idioma') ?? 'pt-br'))
    ) ?? 1;
    $tecnologiaOrigem = $request->filled('origem')
        ? Tecnologias_idiomas::findOrFail($request->input('origem'))
        : null;
    
    /* ✅ Valida se o idioma está na lista permitida
    if (!in_array($idioma, $idiomasDisponiveis)) {
        return back()->withErrors(['idioma' => 'Idioma inválido.']);
    } */

    $validated = $request->validate([
        'titulo' => 'required|string|max:255',
        'idioma' => 'required',
        'origem' => 'nullable|exists:tecnologias_idiomas,id',
        'unidade_id' => 'nullable|exists:unidades,id',
        'numero_caso_fiocruz' => 'nullable|string|max:255',
        'data_submissao' => 'required|date',
        'resumo_solucao' => 'required|string|max:180',
        'problema' => 'required|string|max:700',
        'solucao' => 'required|string|max:700',
        'o_que_buscam' => 'nullable|string',
        'inventores' => 'nullable|array',
        'inventores.*.nome' => 'nullable|string|max:255',
        'inventores.*.coordenador' => 'nullable|boolean',
        'inventores.*.lattes' => 'nullable|url|max:500',
        'inventores.*.linkedin' => 'nullable|url|max:500',
        'doencas' => 'nullable|array|max:5',
        'doencas.*' => 'exists:doencas,id',
        'palavras_chave' => 'nullable|array|max:5',
        'palavras_chave.*' => 'required|string|max:255',
        'tipo_propriedade_id' => 'nullable|array',
        'tipo_propriedade_id.*' => 'nullable|integer|exists:tipo_propriedade,id',
        'pi_descricao' => 'nullable|array',
        'pi_descricao.*' => 'nullable|string|max:2000',
        'pi_link' => 'nullable|array',
        'pi_link.*' => 'nullable|string|max:500',
        'diferenciais' => 'nullable|array',
        'diferenciais.*.id' => 'nullable|exists:diferenciais,id',
        'diferenciais.*.nome' => 'nullable|string|max:40',
        'diferenciais.*.tipo' => 'nullable|string|in:padrao,personalizado',
        'diferenciais.*.icone' => 'nullable|string|max:100',
        'diferenciais.*.id_idioma' => 'nullable|exists:idiomas,id',
        'tipo_tecnologia' => 'nullable|exists:categorias,id',
        'estagio_id' => 'nullable|exists:estagios,id',
        'imagem_lateral' => 'nullable|image|max:81920',
        'url_video' => 'nullable|url',
        'possui_pi' => 'required|boolean',
        
        'descricao_imagem_video' => 'nullable|string',

    ]);

    $numeroCasoInformado = $validated['numero_caso_fiocruz'] ?? null;

    if (! $tecnologiaOrigem && filled($numeroCasoInformado)) {
        $numeroCasoJaExiste = TecnologiaPai::where('numero_caso_fiocruz', $numeroCasoInformado)->exists()
            || Tecnologias_idiomas::where('numero_caso_fiocruz', $numeroCasoInformado)->exists();

        if ($numeroCasoJaExiste) {
            return back()
                ->withErrors(['numero_caso_fiocruz' => 'Este número de caso Fiocruz já está cadastrado.'])
                ->withInput();
        }
    }

    $numeroCaso = $tecnologiaOrigem?->numero_caso_fiocruz ?? $numeroCasoInformado;

    $slugBase = Str::slug($validated['titulo']);
    $slug = $slugBase;
    $count = 1;
    while (Tecnologias_idiomas::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $count++;
    }

    $situacaoNome = match ($request->input('action')) {
    'submit' => 'Publicado',
    'gestec' => 'Validação Gestec',
    default => 'Rascunho',
};
    $situacaoId = Situacao::firstWhere('nome', $situacaoNome)?->id
        ?? Situacao::firstOrCreate(['nome' => $situacaoNome])->id;

    $tecnologia = null;
    $tecnologiasIdiomas = null;

    DB::transaction(function () use (
    $validated,
    $numeroCaso,
    $slug,
    $situacaoId,
    $request,
    $idioma,
    $tecnologiaOrigem,
    &$tecnologia,
    &$tecnologiasIdiomas
    
) 
{   // Versões usam o mesmo registro pai; tecnologias novas criam um pai.
    if ($tecnologiaOrigem) {
        $tecnologiaId = $tecnologiaOrigem->tecnologia_id;
    } else {
        $tecnologiaId = DB::table('tecnologias')->insertGetId([
            'titulo' => $validated['titulo'],
            'numero_caso_fiocruz' => $numeroCaso,
        ]);

        TecnologiaPai::findOrFail($tecnologiaId);
    }

   /* // Gera o drupal_nid //////nao mais necessario.
    $ultimoDrupalNid = DB::table('tecnologias_idiomas')
        ->max('drupal_nid') ?? 0;

    //$novoDrupalNid = $ultimoDrupalNid + 1;

   while (
        DB::table('tecnologias_idiomas')
            ->where('drupal_nid', $novoDrupalNid)
            ->exists()
    ) {
        $novoDrupalNid++;
    }
*/
    // Cria a tradução
    $tecnologiasIdiomas = Tecnologias_idiomas::create([
        'tecnologia_id' => $tecnologiaId,
        'titulo' => $validated['titulo'],
            'idioma' => $idioma,
        'unidade_id' => $validated['unidade_id'] ?? null,
        'numero_caso_fiocruz' => $numeroCaso,
        'data_submissao' => $validated['data_submissao'],
        'resumo_solucao' => $validated['resumo_solucao'],
        'problema' => $validated['problema'],
        'solucao' => $validated['solucao'],
        'o_que_buscam' => $validated['o_que_buscam'] ?? null,
        'situacao_id' => $situacaoId,
        'id_status' => $situacaoId,
        'slug' => $slug,
        'id_user_criador' => Auth::id(),
        'possui_pi' => $validated['possui_pi'],
        //'drupal_nid' => $novoDrupalNid,
        'url_youtube' => $validated['url_video'] ?? $tecnologia->url_youtube ?? '',
        'descricao_imagem_video' =>
        $validated['descricao_imagem_video']
        ?? $tecnologia->descricao_imagem_video
        ?? '',
    ]);

    $tecnologia = $tecnologiasIdiomas;

    // Upload da imagem
    if ($request->hasFile('imagem_lateral')) {
        $path = $request->file('imagem_lateral')
            ->store('tecnologias', 'public');
            $tecnologiasIdiomas->update([
            'imagem_lateral' => $path,
        ]);
    }
        // Sincroniza múltiplas categorias
        if ($request->has('categorias_multiplas')) {
            $categoriasIds = $request->input('categorias_multiplas');
            $syncData = [];
            foreach ($categoriasIds as $catId) {
                $syncData[$catId] = ['estagio_id' => $request->input('estagio_id')];
            }
            $tecnologia->categorias()->sync($syncData);
        } else {
            $tecnologia->categorias()->detach();
        }

    $tecnologiasIdiomas->update([
        'estagio_id' => $validated['estagio_id'] ?? null
    ]);

    $tecnologiasIdiomas->doencas()->sync($validated['doencas'] ?? []);
    $this->syncPalavrasChave(
    $tecnologiasIdiomas,
    $validated
);

    $idsParaSinc = [];
    foreach ($validated['diferenciais'] ?? [] as $diff) {
        if (($diff['tipo'] ?? 'padrao') === 'personalizado' && !empty($diff['nome'])) {
            $modelo = Diferencial::firstOrCreate(
                ['nome' => trim($diff['nome'])],
                ['icone' => $diff['icone'] ?? 'help', 'id_idioma' => $diff['id_idioma'] ?? $idioma]
            );
            $idsParaSinc[] = $modelo->id;
        } elseif (!empty($diff['id'])) {
            $idsParaSinc[] = (int) $diff['id'];
        }
    }
    $tecnologiasIdiomas->diferenciais()->sync($idsParaSinc);
    $this->syncPropriedadesIntelectuais($tecnologiasIdiomas, $validated);
    $this->syncInventores($tecnologiasIdiomas, $validated);
});

//    TecnologiaActivity::logCreated($tecnologiasIdiomas->tecnologia);
    TecnologiaActivity::logCreated($tecnologia);

    return redirect()
        ->route('dashboard')
        ->with('success', 'Tecnologia criada com sucesso!');
}

    public function update(Request $request, Tecnologias_idiomas $tecnologia)
    {
        $situacaoAnterior = $tecnologia->situacao;

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'idioma' => 'required',
            'unidade_id' => 'nullable|exists:unidades,id',
            'numero_caso_fiocruz' => 'nullable|string|max:255',
            'data_submissao' => 'required|date',
            'resumo_solucao' => 'required|string|max:180',
            'problema' => 'required|string|max:700',
            'solucao' => 'required|string|max:700',
            'o_que_buscam' => 'nullable|string',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
            'inventores' => 'nullable|array',
            'inventores.*.nome' => 'nullable|string|max:255',
            'inventores.*.coordenador' => 'nullable|boolean',
            'inventores.*.lattes' => 'nullable|url|max:500',
            'inventores.*.linkedin' => 'nullable|url|max:500',
            'doencas' => 'nullable|array|max:5',
            'doencas.*' => 'exists:doencas,id',
            'palavras_chave' => 'nullable|array|max:5',
            'palavras_chave.*' => 'required|string|max:255',            
            'diferenciais' => 'nullable|array',
            'diferenciais.*.id' => 'nullable|exists:diferenciais,id',
            'diferenciais.*.nome' => 'nullable|string|max:40',
            'diferenciais.*.tipo' => 'nullable|string|in:padrao,personalizado',
            'diferenciais.*.icone' => 'nullable|string|max:100',
            'diferenciais.*.descricao' => 'nullable|string|max:200',
            'diferenciais.*.id_idioma' => 'nullable|exists:idiomas,id',
            'tipo_propriedade_id' => 'nullable|array',
            'tipo_propriedade_id.*' => 'nullable|integer|exists:tipo_propriedade,id',
            'pi_descricao' => 'nullable|array',
            'pi_descricao.*' => 'nullable|string|max:2000',
            'pi_link' => 'nullable|array',
            'pi_link.*' => 'nullable|string|max:500',
            'tipo_tecnologia' => 'nullable|exists:categorias,id',
            'estagio_id' => [
                'nullable',
                Rule::exists('estagios', 'id')->where(function ($query) use ($tecnologia) {
                    $idiomaId = $this->getIdiomaId($tecnologia->idioma ?? 'pt-br');
                    return $query->where(function ($query) use ($idiomaId) {
                        $query->where('id_idioma', $idiomaId)
                            ->orWhereNull('id_idioma');
                    });
                }),
            ],
            'situacao_id' => 'nullable|exists:situacoes,id',
            'possui_pi' => 'required|boolean',
            'imagem_lateral' => 'nullable|image|max:81920',
            'url_video' => 'nullable|url',
            'descricao_imagem_video' => 'nullable|string',
        ]);

        $idiomaId = Idioma::getIdBySigla((string) $validated['idioma']) ?? 1;

        $tecnologia->update([
            'titulo' => $validated['titulo'],
            'idioma' => $idiomaId,
            'unidade_id' => $validated['unidade_id'] ?? null,
            'numero_caso_fiocruz' => $validated['numero_caso_fiocruz'] ?? null,
            'data_submissao' => $validated['data_submissao'],
            'resumo_solucao' => $validated['resumo_solucao'],
            'problema' => $validated['problema'],
            'solucao' => $validated['solucao'],
            'o_que_buscam' => $validated['o_que_buscam'] ?? null,
            'situacao_id' => $this->situacaoIdParaAcao($request, $validated['situacao_id'] ?? $tecnologia->situacao_id),
            'possui_pi' => $validated['possui_pi'],
            'url_youtube' => $validated['url_video'] ?? ($tecnologia->url_youtube ?? ''),
            'descricao_imagem_video' => $validated['descricao_imagem_video']
            ?? $tecnologia->descricao_imagem_video
            ?? '',        ]);

       // Sincroniza múltiplas categorias
            if ($request->has('categorias_multiplas')) {
                $categoriasIds = $request->input('categorias_multiplas');
                $syncData = [];
                foreach ($categoriasIds as $catId) {
                    $syncData[$catId] = ['estagio_id' => $request->input('estagio_id')];
                }
                $tecnologia->categorias()->sync($syncData);
            } else {
                $tecnologia->categorias()->detach();
            }

        $tecnologia->update(['estagio_id' => $validated['estagio_id'] ?? null]);

        // Sincronizar doenças
        if (!empty($validated['doencas'])) {
            $tecnologia->doencas()->sync($validated['doencas']);
        } else {
            $tecnologia->doencas()->detach();
        }

        // criar palavras chaves novas 
        $this->syncPalavrasChave(
        $tecnologia,
        $validated
        );

        // Sincronizar diferenciais
        if (!empty($validated['diferenciais'])) {
            $idsParaSinc = [];
            $idiomaId = $this->getIdiomaId($tecnologia->idioma ?? 'pt-br');

            foreach ($validated['diferenciais'] as $diff) {
                $tipo = $diff['tipo'] ?? 'padrao';

                if ($tipo === 'personalizado' && !empty($diff['nome'])) {
                    $modelo = Diferencial::firstOrCreate(
                        ['nome' => trim($diff['nome'])],
                        ['icone' => $diff['icone'] ?? 'help', 'id_idioma' => $diff['id_idioma'] ?? $idiomaId]
                    );
                    $idsParaSinc[] = $modelo->id;
                } elseif (!empty($diff['id'])) {
                    $idsParaSinc[] = (int) $diff['id'];
                }
            }

            if (!empty($idsParaSinc)) {
                $tecnologia->diferenciais()->sync($idsParaSinc);
            } else {
                $tecnologia->diferenciais()->detach();
            }
        } else {
            $tecnologia->diferenciais()->detach();
        }

        $this->syncPropriedadesIntelectuais($tecnologia, $validated);
        $this->syncInventores($tecnologia, $validated);

        // Upload imagem
        if ($request->hasFile('imagem_lateral')) {
            $path = $request->file('imagem_lateral')->store('tecnologias', 'public');
            $tecnologia->update(['imagem_lateral' => $path]);
        }

        $tecnologia->refresh();
        TecnologiaActivity::logUpdated($tecnologia, $situacaoAnterior);

        return redirect()
            ->route('technology.show', $tecnologia)
            ->with('success', 'Tecnologia atualizada com sucesso!');
    }
    private function syncPropriedadesIntelectuais(

    Tecnologias_idiomas $tecnologia,

    array $validated

): void {

    // Remove as propriedades antigas

    $tecnologia->propriedadesIntelectuais()->delete();

    // 0 = não possui PI

    if ((int) $validated['possui_pi'] === 0) {

        return;

    }

    // 1 = possui PI

    foreach ($validated['tipo_propriedade_id'] ?? [] as $index => $tipoId) {

        if (empty($tipoId)) {

            continue;

        }

        $descricao = $validated['pi_descricao'][$index] ?? null;

        $link = $validated['pi_link'][$index] ?? null;

        $tipo = TipoPropriedade::find($tipoId);

        $tecnologia->propriedadesIntelectuais()->create([

            'tecnologia_id' => $tecnologia->tecnologia_id,

            'possui_pi' => 1,

            'tipo_propriedade_id' => $tipoId,

            'tipo' => $tipo?->nome ?? 'Não definido',

            'descricao' => $descricao,

            'link' => $link,

            'link_propriedade' => $link,

        ]);

    }

}
private function syncPalavrasChave(
    Tecnologias_idiomas $tecnologia,
    array $validated
): void {
    $palavrasIds = [];

    $idiomaId = Idioma::getIdBySigla(
        (string) ($validated['idioma'] ?? 'pt-br')
    ) ?? 1;

    foreach ($validated['palavras_chave'] ?? [] as $valor) {

        $valor = trim((string) $valor);

        if ($valor === '') {
            continue;
        }

        // Palavra existente
        if (ctype_digit($valor)) {

            $palavra = PalavraChave::where('id', (int) $valor)
                ->where('id_idioma', $idiomaId)
                ->first();

            if ($palavra) {
                $palavrasIds[] = $palavra->id;
            }

            continue;
        }

        // Palavra nova
        $palavra = PalavraChave::firstOrCreate(
            [
                'nome' => $valor,
                'id_idioma' => $idiomaId,
            ]
        );

        $palavrasIds[] = $palavra->id;
    }

    $palavrasIds = array_values(array_unique($palavrasIds));
    $palavrasIds = array_slice($palavrasIds, 0, 5);

    $tecnologia->palavrasChave()->sync($palavrasIds);
}
    private function syncInventores(Tecnologias_idiomas $tecnologia, array $validated): void
    {
        
        $tecnologia->inventores()->delete();

        foreach ($validated['inventores'] ?? [] as $inventor) {
            if (blank($inventor['nome'] ?? null)) {
                continue;
            }

            $tecnologia->inventores()->create([
                'tecnologia_id' => $tecnologia->tecnologia_id,
                'tecnologias_idiomas_id' => $tecnologia->id,
                'nome' => trim($inventor['nome']),
                'coordenador' => (bool) ($inventor['coordenador'] ?? false),
                'lattes' => $inventor['lattes'] ?? null,
                'linkedin' => $inventor['linkedin'] ?? null,
            ]);
        }
    }


    public function destroy(Tecnologias_idiomas $tecnologia)
    {
        $tecnologia->delete();
        TecnologiaActivity::logDeleted($tecnologia);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tecnologia excluída com sucesso!');
    }

    public function restore(int $id)
    {
        $tecnologia = Tecnologias_idiomas::onlyTrashed()->findOrFail($id);
        $tecnologia->restore();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tecnologia restaurada com sucesso!');
    }

    public function forceDelete(int $id)
    {
        $tecnologia = Tecnologias_idiomas::onlyTrashed()->findOrFail($id);

        TecnologiaActivity::logDeleted($tecnologia);
        $tecnologia->forceDelete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tecnologia excluída definitivamente.');
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    private function situacaoIdParaAcao(Request $request, ?int $situacaoAtualId): ?int
    {
        return match ($request->input('action')) {
            'save' => $situacaoAtualId
                ?? Situacao::firstWhere('nome', 'Rascunho')?->id
                ?? Situacao::firstOrCreate(['nome' => 'Rascunho'])->id,
            'submit' => Situacao::firstWhere('nome', 'Publicado')?->id
                ?? Situacao::firstOrCreate(['nome' => 'Publicado'])->id,
            default => $situacaoAtualId,
        };
    }

    private function estagiosPorCategoria(?int $idiomaId, $categorias): array
    {
        $idiomaId = $idiomaId ?? 1;

        $categoriasPorNome = $categorias->keyBy(function ($categoria) {
            return Str::lower(Str::ascii(trim($categoria->nome)));
        });

        return Estagio::query()
            ->where(function ($query) use ($idiomaId) {
                $query->where('id_idioma', $idiomaId)
                    ->orWhereNull('id_idioma');
            })
            ->orderBy('id_categoria')
            ->orderBy('nome')
            ->get()
            ->groupBy(function ($estagio) use ($categoriasPorNome) {
                if (is_numeric($estagio->id_categoria)) {
                    return (string) $estagio->id_categoria;
                }

                $categoriaNome = Str::lower(Str::ascii(trim((string) $estagio->id_categoria)));

                return optional($categoriasPorNome->get($categoriaNome))->id;
            })
            ->filter(fn ($grupo, $categoriaId) => filled($categoriaId))
            ->map(function ($grupo) {
                return $grupo->map(function ($estagio) {
                    return [
                        'id' => $estagio->id,
                        'nome' => $estagio->nome,
                        'descricao' => $estagio->descricao,
                    ];
                })->values()->all();
            })
            ->toArray();
    }


    private function getIdiomaId(?string $idioma): int
    {
        return Idioma::getIdBySigla($idioma ?? 'pt-br') ?? 1;
    }

    private function getIdiomasDisponiveis(): array
    {
        return Idioma::getAllSiglas();
    }

    private function isIdiomaValido(string $idioma): bool
    {
        return in_array($idioma, $this->getIdiomasDisponiveis(), true);
    }
   
}
