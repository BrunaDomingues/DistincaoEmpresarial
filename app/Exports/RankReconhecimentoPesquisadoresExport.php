<?php

namespace App\Exports;

use App\Services\RankReconhecimentoPesquisadoresService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankReconhecimentoPesquisadoresExport implements FromCollection, WithHeadings
{
    /** @var array{fatores: Collection, ranking: list<array>, por_pergunta: list<array>}|null */
    private ?array $dados = null;

    public function __construct(
        protected int $formularioId,
        protected string $formularioTitulo,
    ) {}

    public function collection(): Collection
    {
        $dados = $this->dados();
        $fatores = $dados['fatores'];
        $linhas = collect();

        foreach ($dados['ranking'] as $i => $row) {
            $linha = [
                'Formulário' => $this->formularioTitulo,
                'Escopo' => 'Geral',
                'Pergunta' => '',
                'Posição' => $i + 1,
                'Pesquisador' => $row['nome'],
                'Total de envios' => $row['total_envios'],
            ];

            foreach ($fatores as $fator) {
                $linha[$fator->titulo] = $row['totais_por_fator'][(int) $fator->id] ?? 0;
            }

            $linha['Total reconhecimento'] = $row['total_reconhecimento'];
            $linhas->push($linha);
        }

        foreach ($dados['por_pergunta'] as $bloco) {
            foreach ($bloco['ranking'] as $i => $row) {
                $linha = [
                    'Formulário' => $this->formularioTitulo,
                    'Escopo' => 'Por pergunta',
                    'Pergunta' => $bloco['pergunta'],
                    'Posição' => $i + 1,
                    'Pesquisador' => $row['nome'],
                    'Total de envios' => '',
                ];

                foreach ($fatores as $fator) {
                    $linha[$fator->titulo] = $row['totais_por_fator'][(int) $fator->id] ?? 0;
                }

                $linha['Total reconhecimento'] = $row['total_reconhecimento'];
                $linhas->push($linha);
            }
        }

        return $linhas;
    }

    public function headings(): array
    {
        $headings = [
            'Formulário',
            'Escopo',
            'Pergunta',
            'Posição',
            'Pesquisador',
            'Total de envios',
        ];

        foreach ($this->dados()['fatores'] as $fator) {
            $headings[] = $fator->titulo;
        }

        $headings[] = 'Total reconhecimento';

        return $headings;
    }

    /** @return array{fatores: Collection, ranking: list<array>, por_pergunta: list<array>} */
    private function dados(): array
    {
        if ($this->dados === null) {
            $this->dados = app(RankReconhecimentoPesquisadoresService::class)
                ->dadosPorFormulario($this->formularioId);
        }

        return $this->dados;
    }
}
