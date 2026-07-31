<?php

namespace App\Exports;

use App\Services\RankEmpresasInsightService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankEmpresasInsightExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected int $formularioId,
        protected string $formularioTitulo,
    ) {}

    public function collection(): Collection
    {
        $service = app(RankEmpresasInsightService::class);
        $dadosAgrupados = $service->dadosAgrupadosPorFormulario($this->formularioId);

        $linhas = collect();

        foreach ($dadosAgrupados as $grupoData) {
            $grupoTitulo = $grupoData['grupo']->titulo ?? '—';

            foreach ($grupoData['perguntas'] as $perguntaData) {
                $perguntaTitulo = $perguntaData['pergunta']->pergunta ?? '—';

                foreach ($perguntaData['opcoes_reconhecimento'] ?? [] as $opcao) {
                    $linhas->push([
                        'Formulário' => $this->formularioTitulo,
                        'Grupo' => $grupoTitulo,
                        'Pergunta' => $perguntaTitulo,
                        'Tipo' => 'Opção sem nome',
                        'Posição' => '',
                        'Nome consolidado' => $opcao['canonical'] ?? '',
                        'Quantidade' => $opcao['total'] ?? 0,
                        'Fator' => $opcao['fator_exibido'] ?? '',
                        'Grafias no banco' => '',
                    ]);
                }

                foreach ($perguntaData['clusters'] as $i => $cluster) {
                    $grafias = collect($cluster['variants'] ?? [])
                        ->map(fn ($v) => ($v['label'] ?? '').' ('.($v['total'] ?? 0).')')
                        ->implode('; ');

                    $linhas->push([
                        'Formulário' => $this->formularioTitulo,
                        'Grupo' => $grupoTitulo,
                        'Pergunta' => $perguntaTitulo,
                        'Tipo' => 'Empresa',
                        'Posição' => $i + 1,
                        'Nome consolidado' => $cluster['canonical'] ?? '',
                        'Quantidade' => $cluster['total'] ?? 0,
                        'Fator' => $cluster['fator_exibido'] ?? '',
                        'Grafias no banco' => $grafias,
                    ]);
                }
            }
        }

        return $linhas;
    }

    public function headings(): array
    {
        return [
            'Formulário',
            'Grupo',
            'Pergunta',
            'Tipo',
            'Posição',
            'Nome consolidado',
            'Quantidade',
            'Fator',
            'Grafias no banco',
        ];
    }
}
