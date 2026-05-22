<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RespondentesPorBairroExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('formulario_respostas_tratadas as frt')
            ->join('formulario_respostas as fr', 'frt.resposta_id', '=', 'fr.id')
            ->join('formulario_perguntas as fp', 'fr.pergunta_id', '=', 'fp.id')
            ->join('formulario_envios as fe', 'fr.formulario_envio_id', '=', 'fe.id') // Join com envios
            ->where('fp.pergunta', 'Bairro')
            ->whereNotNull('frt.resposta_tratada')
            ->where('fe.invalido', 0) // Filtra só envios válidos
            ->select('frt.resposta_tratada as bairro', DB::raw('COUNT(*) as total_respondentes'))
            ->groupBy('frt.resposta_tratada')
            ->orderByDesc('total_respondentes')
            ->get();
    }


    public function headings(): array
    {
        return ['Bairro', 'Total de Respondentes'];
    }
}
