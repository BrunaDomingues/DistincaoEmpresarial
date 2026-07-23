<?php

namespace App\Support;

use App\Models\InsightEmpresaAlias;

class InsightEmpresaAliasLoader
{
    /** @return array<string, string> */
    public static function map(): array
    {
        $fromConfig = (array) config('insight_empresa_aliases', []);

        try {
            $fromDb = InsightEmpresaAlias::query()
                ->pluck('nome_canonico', 'termo_normalizado')
                ->all();
        } catch (\Throwable) {
            $fromDb = [];
        }

        return array_merge($fromConfig, $fromDb);
    }
}
