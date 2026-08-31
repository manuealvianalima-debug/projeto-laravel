<?php

namespace App\Support;

use App\Models\Estagio;

class EstagioCatalog
{
    /**
     * @return array<int, array{id:int,nome:string,id_categoria:mixed}>
     */
    public static function agrupadosParaFrontend(): array
    {
        return Estagio::query()
            ->orderBy('nome')
            ->get()
            ->map(fn ($estagio) => [
                'id' => $estagio->id,
                'nome' => $estagio->nome,
                'id_categoria' => $estagio->id_categoria,
            ])
            ->values()
            ->all();
    }
}