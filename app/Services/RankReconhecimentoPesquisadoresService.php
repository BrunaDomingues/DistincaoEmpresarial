<?php

namespace App\Services;

use App\Models\FormularioEnvio;
use App\Models\FormularioFatorSatisfacao;
use App\Models\FormularioPergunta;
use App\Models\FormularioResposta as Resposta;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RankReconhecimentoPesquisadoresService
{
    /**
     * Ranking por pesquisador das opções sem nome de empresa
     * (Não conheço / Conheço mas não lembro).
     *
     * @return array{
     *   fatores: Collection<int, FormularioFatorSatisfacao>,
     *   ranking: list<array{
     *     usuario_id: int,
     *     nome: string,
     *     total_envios: int,
     *     totais_por_fator: array<int, int>,
     *     total_reconhecimento: int
     *   }>,
     *   por_pergunta: list<array{
     *     pergunta_id: int,
     *     pergunta: string,
     *     ranking: list<array{
     *       usuario_id: int,
     *       nome: string,
     *       totais_por_fator: array<int, int>,
     *       total_reconhecimento: int
     *     }>
     *   }>
     * }
     */
    public function dadosPorFormulario(int $formularioId): array
    {
        $fatores = FormularioFatorSatisfacao::query()
            ->where('formulario_id', $formularioId)
            ->where('resposta_obrigatoria', false)
            ->orderBy('id')
            ->get(['id', 'titulo']);

        $fatorIds = $fatores->pluck('id')->map(fn ($id) => (int) $id)->all();

        $enviosPorUsuario = FormularioEnvio::query()
            ->select('usuario_id', DB::raw('count(*) as total_envios'))
            ->where('formulario_id', $formularioId)
            ->where('invalido', false)
            ->whereNotNull('usuario_id')
            ->groupBy('usuario_id')
            ->pluck('total_envios', 'usuario_id');

        $nomes = User::query()
            ->whereIn('id', $enviosPorUsuario->keys())
            ->pluck('name', 'id');

        $contagens = $this->contagensPorPesquisador($formularioId, $fatorIds);
        $ranking = $this->montarRanking($contagens, $fatores, $enviosPorUsuario, $nomes);

        $perguntas = FormularioPergunta::query()
            ->select('formulario_perguntas.id', 'formulario_perguntas.pergunta')
            ->join('formulario_passos', 'formulario_passos.id', '=', 'formulario_perguntas.passo_id')
            ->where('formulario_passos.formulario_id', $formularioId)
            ->where('formulario_perguntas.usa_fatores_satisfacao', true)
            ->orderBy('formulario_passos.ordem')
            ->orderBy('formulario_perguntas.id')
            ->get();

        $porPergunta = [];
        foreach ($perguntas as $pergunta) {
            $contagensPergunta = $this->contagensPorPesquisador(
                $formularioId,
                $fatorIds,
                (int) $pergunta->id
            );

            if ($contagensPergunta->isEmpty()) {
                continue;
            }

            $porPergunta[] = [
                'pergunta_id' => (int) $pergunta->id,
                'pergunta' => (string) $pergunta->pergunta,
                'ranking' => $this->montarRanking($contagensPergunta, $fatores),
            ];
        }

        return [
            'fatores' => $fatores,
            'ranking' => $ranking,
            'por_pergunta' => $porPergunta,
        ];
    }

    /**
     * @param  list<int>  $fatorIds
     * @return Collection<int, object{usuario_id: int, nome: string, fator_id: int, total: int}>
     */
    private function contagensPorPesquisador(int $formularioId, array $fatorIds, ?int $perguntaId = null): Collection
    {
        if ($fatorIds === []) {
            return collect();
        }

        $query = Resposta::query()
            ->join('formulario_envios', 'formulario_envios.id', '=', 'formulario_respostas.formulario_envio_id')
            ->join('users', 'users.id', '=', 'formulario_envios.usuario_id')
            ->where('formulario_envios.formulario_id', $formularioId)
            ->where('formulario_envios.invalido', false)
            ->whereIn('formulario_respostas.fator_id', $fatorIds)
            ->select(
                'formulario_envios.usuario_id',
                'users.name as nome',
                'formulario_respostas.fator_id',
                DB::raw('count(*) as total')
            )
            ->groupBy('formulario_envios.usuario_id', 'users.name', 'formulario_respostas.fator_id');

        if ($perguntaId !== null) {
            $query->where('formulario_respostas.pergunta_id', $perguntaId);
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, object{usuario_id: int, nome: string, fator_id: int, total: int}>  $contagens
     * @param  Collection<int, FormularioFatorSatisfacao>  $fatores
     * @param  Collection<int|string, int|string>|null  $enviosPorUsuario
     * @param  Collection<int|string, string>|null  $nomes
     * @return list<array{usuario_id: int, nome: string, total_envios: int, totais_por_fator: array<int, int>, total_reconhecimento: int}>
     */
    private function montarRanking(
        Collection $contagens,
        Collection $fatores,
        ?Collection $enviosPorUsuario = null,
        ?Collection $nomes = null
    ): array {
        $porUsuario = [];

        foreach ($contagens as $linha) {
            $usuarioId = (int) $linha->usuario_id;

            if (! isset($porUsuario[$usuarioId])) {
                $totaisPorFator = [];
                foreach ($fatores as $fator) {
                    $totaisPorFator[(int) $fator->id] = 0;
                }

                $porUsuario[$usuarioId] = [
                    'usuario_id' => $usuarioId,
                    'nome' => (string) $linha->nome,
                    'total_envios' => (int) ($enviosPorUsuario[$usuarioId] ?? 0),
                    'totais_por_fator' => $totaisPorFator,
                    'total_reconhecimento' => 0,
                ];
            }

            $fatorId = (int) $linha->fator_id;
            $total = (int) $linha->total;
            $porUsuario[$usuarioId]['totais_por_fator'][$fatorId] = $total;
            $porUsuario[$usuarioId]['total_reconhecimento'] += $total;
        }

        if ($enviosPorUsuario !== null) {
            foreach ($enviosPorUsuario as $usuarioId => $totalEnvios) {
                $usuarioId = (int) $usuarioId;
                if (isset($porUsuario[$usuarioId])) {
                    continue;
                }

                $totaisPorFator = [];
                foreach ($fatores as $fator) {
                    $totaisPorFator[(int) $fator->id] = 0;
                }

                $porUsuario[$usuarioId] = [
                    'usuario_id' => $usuarioId,
                    'nome' => (string) ($nomes[$usuarioId] ?? '—'),
                    'total_envios' => (int) $totalEnvios,
                    'totais_por_fator' => $totaisPorFator,
                    'total_reconhecimento' => 0,
                ];
            }
        }

        $ranking = array_values($porUsuario);
        usort($ranking, function ($a, $b) {
            $cmp = $b['total_reconhecimento'] <=> $a['total_reconhecimento'];
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcasecmp($a['nome'], $b['nome']);
        });

        return $ranking;
    }
}
