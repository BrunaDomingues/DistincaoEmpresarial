<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class RelatorioBairroQuery
{
    public static function base(): Builder
    {
        return DB::table('formulario_envios as fe')
            ->where('fe.invalido', 0)
            ->whereNotNull('fe.bairro')
            ->whereRaw("TRIM(fe.bairro) <> ''");
    }

    public static function agrupadoPorBairro(): Builder
    {
        return self::base()
            ->select(
                DB::raw('TRIM(fe.bairro) as bairro'),
                DB::raw('COUNT(fe.id) as total_respondentes')
            )
            ->groupBy(DB::raw('TRIM(fe.bairro)'));
    }
}
