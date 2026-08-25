<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\LocalidadeCoordenadas;

class DashboardController extends Controller
{
    public function index()
    {
        // verifica se o uusuário é admin através do middleware
        if (!Auth::user()->is_admin) {
            // envia os valores vazios
            $envios = [];
            $totalEnvios = 0;
            $totalQuestionarios = 0;
            $totalBairros = 0;
            $totalUsuarios = 0;
            $enviosRecentes = [];
            $dadosPorBairro = [];
            $pontosMapaJson = [];
            $totalEnviosComGeo = 0;
            $graficoGeoLabels = [];
            $graficoGeoDatasets = [];

            return view('dashboard', compact(
                'envios',
                'totalEnvios',
                'totalQuestionarios',
                'totalBairros',
                'totalUsuarios',
                'enviosRecentes',
                'dadosPorBairro',
                'pontosMapaJson',
                'totalEnviosComGeo',
                'graficoGeoLabels',
                'graficoGeoDatasets'
            ));
        }

        // Total de envios por usuário
        $envios = DB::table('formulario_envios')
            ->select('users.name', DB::raw('count(formulario_envios.id) as total_envios'))
            ->join('users', 'formulario_envios.usuario_id', '=', 'users.id')
            ->where('formulario_envios.invalido', false)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_envios')
            ->get();

        // Total geral de envios
        $totalEnvios = DB::table('formulario_envios')->where('formulario_envios.invalido', false)->count();

        // Total de questionários
        $totalQuestionarios = DB::table('formularios')->count();

        // Total de bairros distintos (geolocalização gravada em formulario_envios)
        $totalBairros = DB::table('formulario_envios')
            ->where('invalido', false)
            ->whereNotNull('bairro')
            ->whereRaw("TRIM(bairro) <> ''")
            ->distinct()
            ->count('bairro');

        // Total de usuários
        $totalUsuarios = DB::table('users')->count();

        // Últimos sete envios: usuário, título do formulário e data/hora (formatada em PHP para compatibilidade com SQLite)
        $enviosRecentes = DB::table('formulario_envios')
            ->select('formulario_envios.id', 'formularios.titulo', 'users.name as usuario', 'formulario_envios.created_at')
            ->join('users', 'formulario_envios.usuario_id', '=', 'users.id')
            ->join('formularios', 'formulario_envios.formulario_id', '=', 'formularios.id')
            ->orderByDesc('formulario_envios.created_at')
            ->limit(10)
            ->get()
            ->map(function ($envio) {
                $envio->data_hora = \Carbon\Carbon::parse($envio->created_at)->format('d/m/Y H:i');
                unset($envio->created_at);

                return $envio;
            });

        // Dados agrupados por bairro e cidade informados no envio
        $dadosPorBairro = DB::table('formulario_envios')
            ->where('invalido', false)
            ->whereNotNull('bairro')
            ->whereRaw("TRIM(bairro) <> ''")
            ->select('bairro', 'cidade', DB::raw('COUNT(id) as total'))
            ->groupBy('bairro', 'cidade')
            ->orderByDesc('total')
            ->get()
            ->map(function ($linha) {
                $bairro = trim((string) ($linha->bairro ?? ''));
                $cidade = trim((string) ($linha->cidade ?? ''));
                $cidade = $cidade !== '' ? $cidade : 'Cidade não informada';

                return (object) [
                    'bairro' => $bairro.' ('.$cidade.')',
                    'total' => (int) $linha->total,
                ];
            })
            ->groupBy(fn ($linha) => mb_strtolower($linha->bairro))
            ->map(function ($grupo) {
                $primeiro = $grupo->first();
                $primeiro->total = $grupo->sum('total');

                return $primeiro;
            })
            ->sortByDesc('total')
            ->values();

        $totalEnviosComGeo = DB::table('formulario_envios')
            ->where('invalido', false)
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->whereNotNull('bairro')->whereRaw("TRIM(bairro) <> ''");
                    })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('cidade')->whereRaw("TRIM(cidade) <> ''");
                    });
            })
            ->count();

        $pontosMapaJson = $this->pontosMapaPorBairroCidade();
        [$graficoGeoLabels, $graficoGeoDatasets] = $this->dadosGraficoGeoPesquisadores();

        return view('dashboard', compact(
            'envios',
            'totalEnvios',
            'totalQuestionarios',
            'totalBairros',
            'totalUsuarios',
            'enviosRecentes',
            'dadosPorBairro',
            'pontosMapaJson',
            'totalEnviosComGeo',
            'graficoGeoLabels',
            'graficoGeoDatasets'
        ));
    }

    /**
     * @return array{0: list<string>, 1: list<array{label: string, data: list<int>, backgroundColor: string}>}
     */
    private function dadosGraficoGeoPesquisadores(): array
    {
        $linhas = DB::table('formulario_envios as fe')
            ->join('users', 'users.id', '=', 'fe.usuario_id')
            ->where('fe.invalido', false)
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->whereNotNull('fe.bairro')->whereRaw("TRIM(fe.bairro) <> ''");
                    })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('fe.cidade')->whereRaw("TRIM(fe.cidade) <> ''");
                    });
            })
            ->select([
                'users.name as pesquisador',
                'fe.bairro',
                'fe.cidade',
                DB::raw('COUNT(fe.id) as total'),
            ])
            ->groupBy('users.id', 'users.name', 'fe.bairro', 'fe.cidade')
            ->orderBy('users.name')
            ->get()
            ->map(function ($linha) {
                $bairro = trim((string) ($linha->bairro ?? ''));
                $cidade = trim((string) ($linha->cidade ?? ''));
                $linha->local = ($bairro !== '' ? $bairro : 'Bairro não informado')
                    .' ('.($cidade !== '' ? $cidade : 'Cidade não informada').')';
                $linha->pesquisador = (string) $linha->pesquisador;

                return $linha;
            });

        $pesquisadores = $linhas->pluck('pesquisador')->unique()->values();
        $locais = $linhas
            ->pluck('local')
            ->unique()
            ->sort(fn ($a, $b) => strcasecmp($a, $b))
            ->values();

        $totais = [];
        foreach ($linhas as $linha) {
            $totais[$linha->local][$linha->pesquisador] = ($totais[$linha->local][$linha->pesquisador] ?? 0) + (int) $linha->total;
        }

        $datasets = $locais->values()->map(function ($local, $indice) use ($pesquisadores, $totais, $locais) {
            return [
                'label' => $local,
                'data' => $pesquisadores->map(fn ($pesquisador) => $totais[$local][$pesquisador] ?? 0)->values()->all(),
                'backgroundColor' => $this->corParaIndice($indice, $locais->count()),
            ];
        })->values()->all();

        return [$pesquisadores->all(), $datasets];
    }

    /**
     * @return list<array{lat: float, lng: float, bairro: string, cidade: string, endereco: string, total: int}>
     */
    private function pontosMapaPorBairroCidade(): array
    {
        $linhas = DB::table('formulario_envios as fe')
            ->where('fe.invalido', false)
            ->where(function ($query) {
                $query
                    ->where(function ($q) {
                        $q->whereNotNull('fe.bairro')->whereRaw("TRIM(fe.bairro) <> ''");
                    })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('fe.cidade')->whereRaw("TRIM(fe.cidade) <> ''");
                    });
            })
            ->select([
                'fe.bairro',
                'fe.cidade',
                DB::raw('COUNT(fe.id) as total'),
            ])
            ->groupBy('fe.bairro', 'fe.cidade')
            ->get();

        $agrupados = [];
        foreach ($linhas as $linha) {
            $bairro = trim((string) ($linha->bairro ?? ''));
            $cidade = trim((string) ($linha->cidade ?? ''));
            $chave = mb_strtolower($cidade).'|'.mb_strtolower($bairro);
            $agrupados[$chave] ??= [
                'bairro' => $bairro,
                'cidade' => $cidade,
                'total' => 0,
            ];
            $agrupados[$chave]['total'] += (int) $linha->total;
        }

        $pontos = [];
        foreach ($agrupados as $local) {
            $coordenada = LocalidadeCoordenadas::ponto($local['cidade'], $local['bairro']);
            if ($coordenada === null) {
                continue;
            }

            $partes = array_filter([
                $local['bairro'] !== '' ? $local['bairro'] : null,
                $local['cidade'] !== '' ? $local['cidade'] : null,
            ]);

            $pontos[] = [
                'lat' => $coordenada['lat'],
                'lng' => $coordenada['lng'],
                'bairro' => $local['bairro'],
                'cidade' => $local['cidade'],
                'endereco' => implode(', ', $partes) ?: 'Local não informado',
                'total' => $local['total'],
            ];
        }

        return $pontos;
    }

    private function corParaIndice(int $indice, int $total): string
    {
        $hue = (int) round(($indice * 360) / max(1, $total));

        return "hsl({$hue}, 62%, 48%)";
    }

    public function enviosPorUsuario()
    {
        $envios = DB::table('formulario_envios')
            ->select('users.name', DB::raw('count(formulario_envios.id) as total_envios'))
            ->join('users', 'formulario_envios.usuario_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_envios')
            ->get();

        return response()->json($envios);
    }
}
