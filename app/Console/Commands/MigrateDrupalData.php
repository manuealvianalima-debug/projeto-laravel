<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Diferencial;
use App\Models\Doenca;
use App\Models\Estagio;
use App\Models\Inventor;
use App\Models\PalavraChave;
use App\Models\PropriedadeIntelectual;
use App\Models\Situacao;
use App\Models\Tecnologia;
use App\Models\TipoPropriedade;
use App\Models\Unidade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Numero_fiocruz;

class MigrateDrupalData extends Command
{
    protected $signature = 'migrate:drupal {file : Caminho do arquivo CSV}';
    protected $description = 'Migra dados do portfolio do Drupal para o Laravel';

   // private array $situacaoCache = []; 
    private array $unidadeCache = [];
    private array $categoriaCache = [];
    private array $estagioMap = [];
    private array $headerMap = [];

    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Arquivo nao encontrado: {$filePath}");
            return self::FAILURE;
        }

        $this->info('Carregando catalogos...');
        $this->loadCatalogs();

        $this->info('Iniciando migracao...');
        $this->processFile($filePath);

        $this->info('Migracao concluida!');
        return self::SUCCESS;
    }

    private function loadCatalogs(): void
    {
        /*foreach (['Rascunho', 'Publicado', 'Publicada', 'Em Analise', 'Validacao Gestec'] as $nome) {
            $this->situacaoCache[$nome] = Situacao::firstOrCreate(['nome' => $nome])->id;
        }*/

        $this->unidadeCache = Unidade::pluck('id', 'nome')
            ->mapWithKeys(fn($id, $nome) => [$this->normalizar($nome) => $id])
            ->toArray();

        $this->categoriaCache = Categoria::pluck('id', 'nome')
            ->mapWithKeys(fn($id, $nome) => [$this->normalizar($nome) => $id])
            ->toArray();

        $this->estagioMap = Estagio::all()
            ->mapWithKeys(fn($e) => [$this->normalizar($e->nome) => $e->id])
            ->toArray();
    }

    private function processFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $bom = pack('H*','EFBBBF');
        if (strncmp($content, $bom, 3) === 0) {
            $content = substr($content, 3);
        }
        
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        $lines = explode("\n", $content);
        
        if (count($lines) < 2) {
            $this->error('Arquivo vazio ou com apenas cabecalho.');
            return;
        }

        $header = str_getcsv(array_shift($lines));
        $this->headerMap = array_flip($header);
        $this->info('Cabecalho: ' . implode(' | ', $header));
        $this->info('Total de linhas: ' . count($lines));

        $success = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($lines as $lineNumber => $line) {
                if (empty(trim($line))) continue;

                $fields = str_getcsv($line);
                
                if (count($fields) < 5) continue;

                try {
                   /* if ($this->isTestRecord($fields)) {
                        $skipped++;
                        $this->line("Linha {$lineNumber}: TESTE - ignorado");
                        continue;
                    }*/
                    
                    $this->migrateLine($fields);
                    $success++;
                    
                    if ($success % 10 === 0) {
                        $this->info("Migrados: {$success}...");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Erro linha {$lineNumber}: " . $e->getMessage());
                }
            }
            DB::commit();
            $this->info("====================================");
            $this->info("Sucesso: {$success} | Ignorados: {$skipped} | Erros: {$errors}");
            $this->info("====================================");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Falha critica: ' . $e->getMessage());
            $this->error('Arquivo: ' . $e->getFile() . ' Linha: ' . $e->getLine());
        }
    }

    private function getField(array $line, string $name): string
    {
        $index = $this->headerMap[$name] ?? null;
        if ($index === null || !isset($line[$index])) return '';
        return trim($line[$index]);
    }

    private function migrateLine(array $line): void
    {
        $legacyNid   = (int) $this->getField($line, 'Nid');
        $legacyUuid  = $this->getField($line, 'uuid');
        $titulo      = $this->getField($line, 'titulo');
        $idiomaRaw   = $this->getField($line, 'Idioma');
        $idioma      = stripos($idiomaRaw, 'english') !== false ? 'en' : 'pt-br';
        $problema    = $this->getField($line, 'problema') ?: 'Sem descricao';
        $solucao     = $this->getField($line, 'solucao') ?: 'Sem descricao';
        $resumo      = $this->getField($line, 'resumo-da-solucao') ?: $titulo;
        $buscamos    = $this->getField($line, 'o-que-buscamos') ?: null;
        $escritoEm   = $this->getField($line, 'Escrito em');
        $alterado    = $this->getField($line, 'Alterado');
        $situacaoRaw = $this->getField($line, 'situacao');
       /* echo $situacaoRaw;*/
       
        switch ($situacaoRaw) { 

            case "Rascunho": 

                $situacao_Final = 1; 

                break; 

            case "Publicado": 

                $situacao_Final = 3; 

                break; 

            case "Validação Gestec": 

                $situacao_Final = 5; 

                break; 

            default: 

                $situacao_Final = 1; 

                break; 

        } 
        /*echo $situacaoRaw."=>".$situacao_Final."/n"; 
        var_dump($situacaoRaw);*/
        
        $diferenciaisRaw = $this->getField($line, 'diferenciais');
        $inventoresRaw   = $this->getField($line, 'inventores');
        $categoriaRaw    = $this->getField($line, 'categorias');
        $estagioRaw      = $this->getField($line, 'estagio-desenvolvimento');
        $propriedadeRaw  = $this->getField($line, 'propriedade-intelectual');
        $doencasRaw      = $this->getField($line, 'Doen-as-relacionadas');
        $unidadeRaw      = $this->getField($line, 'unidade');
        $palavrasRaw     = $this->getField($line, 'Palavras-chave');
        
        
        $numero_caso_fiocruz=
        $this->getField($line, 'Número_de_caso_Fiocruz'); 
        echo $numero_caso_fiocruz."-"; 
               

        if (empty($legacyNid)) return;

        $tituloFinal = $titulo ?: $resumo ?: 'Tecnologia ' . $legacyNid;

        $tecnologia = Tecnologia::where('drupal_nid', $legacyNid)->first()
            ?? new Tecnologia();

        $tituloFinal = $titulo ?: $resumo ?: 'Tecnologia ' . $legacyNid;

        $slug = $this->uniqueSlug(
            $tituloFinal,
            $tecnologia->exists ? $tecnologia->id : null
        );
        $tecnologia->fill([
    'titulo'         => Str::limit($tituloFinal, 255),

    'idioma'         => $idioma,

    'numero_caso'    => $legacyNid,
       

    'numero_caso_fiocruz' =>$numero_caso_fiocruz,
                
    'data_submissao' => $this->parseDate($escritoEm)
        ?? now()->format('Y-m-d'),

    'resumo_solucao' => Str::limit(
        $resumo ?: $tituloFinal,
        180
    ),

    'problema'       => $problema,
    'situacao_id'       => $situacao_Final,
    'solucao'        => $solucao,

    'o_que_buscam'   => $buscamos,

    'slug'           => $slug,

    'drupal_nid'     => $legacyNid,

    'drupal_uuid'    => $legacyUuid ?: null,

    'possui_pi'      => !empty(
        trim($propriedadeRaw, " \¨\"-")
    ),
]);

        $unidadeId = $this->findOrCreateUnidade($unidadeRaw);
        if ($unidadeId) $tecnologia->unidade_id = $unidadeId;

       // $tecnologia->situacao_id = ($situacaoRaw);
       
        $tecnologia->estagio_id = $this->findEstagio($estagioRaw);
        $tecnologia->save();

        $this->syncCategorias($tecnologia, $categoriaRaw);
        $this->syncDoencas($tecnologia, $doencasRaw);
        $this->syncPalavras($tecnologia, $palavrasRaw);
        $this->syncDiferenciais($tecnologia, $diferenciaisRaw, $idioma);
        $this->syncInventores($tecnologia, $inventoresRaw);
        $this->syncPropriedades($tecnologia, $propriedadeRaw);

        DB::table('tecnologias_migration_log')->updateOrInsert(
            ['tecnologia_id' => $tecnologia->id],
            [
                'drupal_created_at' => $this->parseDate($escritoEm),
                'drupal_updated_at' => $this->parseDate($alterado),
                'raw_drupal_data'   => json_encode($line, JSON_UNESCAPED_UNICODE),
                'imported_at'       => now(),
            ]
        );
    }

    private function isTestRecord(array $line): bool
    {
        $testWords = ['isto é um teste', 'teste 1', 'teste 2', 'testestes', 'jkdwhbhjb', 'uwhudb', 'dfgdfgdfg', 'teste marcelo'];
        $check = strtolower(implode(' ', array_slice($line, 0, 10)));
        foreach ($testWords as $w) {
            if (str_contains($check, $w)) return true;
        }
        return false;
    }

    private function normalizar(string $s): string
    {
        return Str::lower(Str::ascii(trim($s)));
    }

   private function uniqueSlug(
    string $text,
    ?int $ignoreId = null
): string
{
    $slug = Str::slug(Str::limit($text, 100));

    if (empty($slug)) {
        $slug = 'tecnologia';
    }

    $original = $slug;
    $count = 1;

    while (
        Tecnologia::where('slug', $slug)
            ->when(
                $ignoreId,
                fn ($q) => $q->where('id', '!=', $ignoreId)
            )
            ->exists()
    ) {
        $slug = $original . '-' . $count++;
    }

    return $slug;
}

    private function parseDate(?string $d): ?string
    {
        if (empty($d)) return null;
        try {
            return \Carbon\Carbon::parse(trim($d))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

   /* private function findSituacao(string $nome): int
    {
        $nome = trim($nome);
        $map = ['Publicado' => 'Publicada'];
        $nome = $map[$nome] ?? $nome;
        return $this->situacaoCache[$nome] ?? $this->situacaoCache['Rascunho'];
    } */ 

    private function findOrCreateUnidade(string $nome): ?int
    {
        $nome = trim($nome);
        if (empty($nome)) return null;
        $key = $this->normalizar($nome);
        if (isset($this->unidadeCache[$key])) return $this->unidadeCache[$key];
        $u = Unidade::firstOrCreate(['nome' => $nome]);
        $this->unidadeCache[$key] = $u->id;
        return $u->id;
    }

    private function findEstagio(string $raw): ?int
    {
        if (empty(trim($raw))) return null;

        // Remove surrounding quotes and whitespace
        $raw = trim($raw, " \t\n\r\0\x0B\"");

        // Normalize common separators (double-hyphen, en-dash, em-dash, single hyphen, colon)
        $separators = ['--', '\x{2013}', '\x{2014}', '-', ':'];
        $regex = '/' . implode('|', $separators) . '/u';
        $parts = preg_split($regex, $raw, 2);

        $texto = trim($parts[1] ?? $parts[0]);
        if (empty($texto)) return null;

        $key = $this->normalizar($texto);

        // Exact match first
        if (isset($this->estagioMap[$key])) return $this->estagioMap[$key];

        // Fuzzy match: look for substrings both ways
        foreach ($this->estagioMap as $k => $id) {
            if (str_contains($k, $key) || str_contains($key, $k)) return $id;
        }

        // Last resort: try matching any token from the raw text against keys
        $tokens = preg_split('/[\s,;]+/', $this->normalizar($texto));
        foreach ($tokens as $token) {
            if (empty($token)) continue;
            foreach ($this->estagioMap as $k => $id) {
                if (str_contains($k, $token) || str_contains($token, $k)) return $id;
            }
        }

        return null;
    }

    private function syncCategorias(Tecnologia $t, string $raw): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        $ids = [];
        $assoc = [];
        foreach (explode('|', $raw) as $nome) {
            $nome = trim($nome);
            if (empty($nome)) continue;
            $key = $this->normalizar($nome);
            if (!isset($this->categoriaCache[$key])) {
                $cat = Categoria::firstOrCreate(['nome' => $nome]);
                $this->categoriaCache[$key] = $cat->id;
            }
            $catId = $this->categoriaCache[$key];
            $ids[] = $catId;

            // Preencher estagio_id no pivot se a tecnologia já tiver estagio definido
            $assoc[$catId] = ['estagio_id' => $t->estagio_id ?? null];
        }

        if (!empty($assoc)) {
            $t->categorias()->sync($assoc);
        } elseif (!empty($ids)) {
            // fallback para versões antigas: sync simples
            $t->categorias()->sync($ids);
        }
    }

    private function syncDoencas(Tecnologia $t, string $raw): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        $ids = [];
        foreach (explode('|', $raw) as $nome) {
            $nome = trim($nome);
            if (empty($nome)) continue;
            $ids[] = Doenca::firstOrCreate(['nome' => $nome])->id;
        }
        if (!empty($ids)) $t->doencas()->sync($ids);
    }

    private function syncPalavras(Tecnologia $t, string $raw): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        $ids = [];
        foreach (explode('|', $raw) as $nome) {
            $nome = trim($nome);
            if (empty($nome)) continue;
            $ids[] = PalavraChave::firstOrCreate(['nome' => $nome])->id;
        }
        if (!empty($ids)) $t->palavrasChave()->sync($ids);
    }

    private function syncDiferenciais(Tecnologia $t, string $raw, string $idioma): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        $idiomaId = $idioma === 'en' ? 2 : 1;
        $blocos = array_filter(explode('¨', $raw), fn($b) => !empty(trim($b)));
        $ids = [];
        foreach ($blocos as $bloco) {
            $partes = explode('--', $bloco, 2);
            $nome = trim($partes[1] ?? '');
            if (empty($nome)) continue;
            $ids[] = Diferencial::firstOrCreate(
                ['nome' => $nome],
                ['id_idioma' => $idiomaId, 'icone' => $partes[0] ?? 'help']
            )->id;
        }
        if (!empty($ids)) $t->diferenciais()->sync($ids);
    }

    private function syncInventores(Tecnologia $t, string $raw): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        
        // Remove inventores antigos
        Inventor::where('tecnologia_id', $t->id)->delete();
        
        $blocos = array_filter(explode('¨', $raw), fn($b) => !empty(trim($b)));
        $idx = 0;
        
        foreach ($blocos as $bloco) {

    $partes = explode('--', $bloco, 3);

        $nome = trim($partes[0] ?? '');

        if (empty($nome)) {
            continue;
        }

        $lattes = null;
        $linkedin = null;

        if (preg_match('/https?:\/\/lattes\.cnpq\.br\/[0-9]+/i', $bloco, $m)) {
            $lattes = $m[0];
        }

        if (preg_match('/https?:\/\/(?:www\.)?linkedin\.com\/[^\s"<]+/i', $bloco, $m)) {
            $linkedin = $m[0];
        }

        Inventor::create([
            'tecnologia_id' => $t->id,
            'nome' => $nome,
            'coordenador' => ($idx === 0),
            'lattes' => $lattes,
            'linkedin' => $linkedin,
        ]);

        $idx++;
    }

    }

    private function syncPropriedades(Tecnologia $t, string $raw): void
    {
        $raw = trim($raw, '"');
        if (empty($raw)) return;
        $t->propriedades_intelectuais()->delete();
        $blocos = array_filter(explode('¨', $raw), fn($b) => !empty(trim($b, " \¨\"-")));
        foreach ($blocos as $bloco) {
            $partes = explode('--', $bloco);
            $tipoNome = trim($partes[0] ?? '');
            if (empty($tipoNome)) continue;
            $tipo = TipoPropriedade::firstOrCreate(['nome' => $tipoNome]);
            $t->propriedades_intelectuais()->create([
                'possui_propriedade' => 1,
                'tipo_propriedade_id' => $tipo->id,
                'tipo' => $tipo->nome,
                'descricao' => trim($partes[1] ?? '') ?: null,
                'link_propriedade' => trim($partes[2] ?? '') ?: null,
                'link' => trim($partes[2] ?? '') ?: null,
            ]);
        }
    }
}
