<?php

namespace App\Console\Commands;

use App\Models\Tecnologia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateImagesFromCsv extends Command
{
    protected $signature = 'images:update 
                            {file : Caminho do arquivo CSV}
                            {--lang=auto : Forçar idioma (pt|en|auto)}';
    
    protected $description = 'Atualiza apenas as imagens das tecnologias a partir do CSV';

    private array $headerMap = [];

    public function handle(): int
    {
        $filePath = $this->argument('file');
        $forceLang = $this->option('lang');

        if (!file_exists($filePath)) {
            $this->error("Arquivo nao encontrado: {$filePath}");
            return self::FAILURE;
        }

        // Detecta idioma
        $idioma = $this->detectarIdioma($filePath);
        if ($forceLang !== 'auto') {
            $idioma = $forceLang;
        }

        $this->info("📄 Processando arquivo: " . basename($filePath));
        $this->info("🌐 Idioma: " . strtoupper($idioma));

        // Lê o CSV
        $content = file_get_contents($filePath);
        
        // Remove BOM
        $bom = pack('H*','EFBBBF');
        if (strncmp($content, $bom, 3) === 0) {
            $content = substr($content, 3);
        }
        
        // Converte para UTF-8
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        $tempFile = tmpfile();
        fwrite($tempFile, $content);
        rewind($tempFile);
        
        $header = fgetcsv($tempFile, 0, ',', '"', '\\');
        if (!$header) {
            $this->error('Arquivo inválido.');
            fclose($tempFile);
            return self::FAILURE;
        }

        $this->headerMap = array_flip($header);
        
        // Verifica se tem os campos necessários
        if (!isset($this->headerMap['Nid']) || !isset($this->headerMap['imagem'])) {
            $this->error('❌ Campos "Nid" ou "imagem" não encontrados!');
            fclose($tempFile);
            return self::FAILURE;
        }

        $atualizados = 0;
        $erros = 0;
        $lineNumber = 0;

        DB::beginTransaction();
        try {
            while (($line = fgetcsv($tempFile, 0, ',', '"', '\\')) !== false) {
                $lineNumber++;
                
                if (count($line) < 2 || empty(implode('', $line))) {
                    continue;
                }

                try {
                    // Pega NID
                    $nidIndex = $this->headerMap['Nid'];
                    $nidValue = trim($line[$nidIndex] ?? '');
                    
                    if (empty($nidValue) || $nidValue === '0') {
                        continue;
                    }

                    // Pega imagem
                    $imgIndex = $this->headerMap['imagem'];
                    $imagemRaw = trim($line[$imgIndex] ?? '');
                    
                    if (empty($imagemRaw)) {
                        continue;
                    }

                    // Corrige a URL
                    $imagemUrl = $this->corrigirUrlImagem($imagemRaw);

                    // Busca a tecnologia por NID + idioma
                    $tecnologia = Tecnologia::where('drupal_nid', (int) $nidValue)
                        ->where('idioma', $idioma)
                        ->first();

                    if (!$tecnologia) {
                        $this->warn("⚠️ Tecnologia NID={$nidValue} não encontrada para idioma {$idioma}");
                        $erros++;
                        continue;
                    }

                    // Atualiza apenas a imagem
                    $tecnologia->imagem_lateral = $imagemUrl;
                    $tecnologia->save();

                    $atualizados++;
                    $this->info("✅ NID={$nidValue}: {$imagemUrl}");

                } catch (\Exception $e) {
                    $erros++;
                    $this->error("❌ Erro linha {$lineNumber}: " . $e->getMessage());
                }
            }
            
            fclose($tempFile);
            DB::commit();
            
            $this->info("====================================");
            $this->info("✅ Atualizados: {$atualizados} | ❌ Erros: {$erros}");
            $this->info("====================================");
            
        } catch (\Exception $e) {
            fclose($tempFile);
            DB::rollBack();
            $this->error('❌ Falha critica: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function detectarIdioma(string $filePath): string
    {
        $nome = basename($filePath);
        
        if (preg_match('/_(en|ingles|eng)(-|_|\.)/i', $nome)) {
            return 'en';
        }
        
        if (preg_match('/_(pt|portugues|por)(-|_|\.)/i', $nome)) {
            return 'pt';
        }
        
        $idioma = $this->ask('Não foi possível detectar o idioma. Digite "pt" ou "en"', 'pt');
        return strtolower($idioma) === 'en' ? 'en' : 'pt';
    }

    private function corrigirUrlImagem(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);
        
        // Se já começa com http, retorna como está
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        // Remove barras no início
        $url = ltrim($url, '/');

        // Se começa com 'sites/', adiciona o domínio
        if (str_starts_with($url, 'sites/')) {
            return 'https://portfoliodeinovacao.fiocruz.br/' . $url;
        }

        // Fallback: adiciona o domínio
        return 'https://portfoliodeinovacao.fiocruz.br/' . $url;
    }
}