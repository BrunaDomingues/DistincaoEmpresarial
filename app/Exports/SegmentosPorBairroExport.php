<?php

namespace App\Exports;

use App\Services\RelatorioSegmentoPorBairroService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SegmentosPorBairroExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected int $segmentoId,
        protected ?string $cidade = null,
    ) {}

    public function collection(): Collection
    {
        $resultado = app(RelatorioSegmentoPorBairroService::class)
            ->dadosPorSegmento($this->segmentoId, $this->cidade);

        $linhas = collect();

        foreach ($resultado['bairros'] as $bairro) {
            foreach ($bairro['clusters'] as $posicao => $cluster) {
                $grafias = collect($cluster['variants'] ?? [])
                    ->map(fn ($variante) => ($variante['label'] ?? '').' ('.($variante['total'] ?? 0).')')
                    ->implode('; ');

                $linhas->push([
                    'Formulário' => $resultado['segmento']->formulario->titulo,
                    'Setor/segmento' => $resultado['segmento']->titulo,
                    'Cidade' => $bairro['cidade'],
                    'Bairro' => $bairro['bairro'],
                    'Posição' => $posicao + 1,
                    'Empresa' => $cluster['canonical'] ?? '',
                    'Menções' => $cluster['total'] ?? 0,
                    'Fator mais citado' => $cluster['fator_exibido'] ?? '',
                    'Grafias consolidadas' => $grafias,
                    'Requer validação' => ! empty($cluster['requer_validacao']) ? 'Sim' : 'Não',
                ]);
            }
        }

        return $linhas;
    }

    public function headings(): array
    {
        return [
            'Formulário',
            'Setor/segmento',
            'Cidade',
            'Bairro',
            'Posição',
            'Empresa',
            'Menções',
            'Fator mais citado',
            'Grafias consolidadas',
            'Requer validação',
        ];
    }
}
