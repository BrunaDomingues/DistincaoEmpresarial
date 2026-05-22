<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            
            return view('dashboard', compact(
                'envios',
                'totalEnvios',
                'totalQuestionarios',
                'totalBairros',
                'totalUsuarios',
                'enviosRecentes',
                'dadosPorBairro'
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

        // Total de bairros distintos
        $bairroIds = DB::table('formulario_perguntas')
        ->where('pergunta', 'like', '%bairro%')
        ->pluck('id'); // retorna um array com todos os IDs

        $totalBairros = DB::table('formulario_respostas_tratadas as frt')
                            ->join('formulario_respostas as fr', 'frt.resposta_id', '=', 'fr.id')
                            ->join('formulario_perguntas as fp', 'fr.pergunta_id', '=', 'fp.id')
                            ->join('formulario_envios as fe', 'fr.formulario_envio_id', '=', 'fe.id')
                            ->where('fp.pergunta', 'Bairro')
                            ->whereNotNull('frt.resposta_tratada')
                            ->where('fe.invalido', false)
                            ->distinct('frt.resposta_tratada')
                            ->count('frt.resposta_tratada');

        // Total de usuários
        $totalUsuarios = DB::table('users')->count();

        // Últimos sete envios: usuário, título do formulário e data/hora (formatada em PHP para compatibilidade com SQLite)
        $enviosRecentes = DB::table('formulario_envios')
            ->select('formularios.titulo', 'users.name as usuario', 'formulario_envios.created_at')
            ->join('users', 'formulario_envios.usuario_id', '=', 'users.id')
            ->join('formularios', 'formulario_envios.formulario_id', '=', 'formularios.id')
            ->orderByDesc('formulario_envios.created_at')
            ->limit(7)
            ->get()
            ->map(function ($envio) {
                $envio->data_hora = \Carbon\Carbon::parse($envio->created_at)->format('d/m/Y H:i');
                unset($envio->created_at);

                return $envio;
            });

        // Dados agrupados por bairro (nome do bairro e quantidade de envios)
        $dadosPorBairro = DB::table('formulario_respostas_tratadas as frt')
                                ->join('formulario_respostas as fr', 'frt.resposta_id', '=', 'fr.id')
                                ->join('formulario_perguntas as fp', 'fr.pergunta_id', '=', 'fp.id')
                                ->join('formulario_envios as fe', 'fr.formulario_envio_id', '=', 'fe.id')
                                ->where('fp.pergunta', 'Bairro') // ou use whereIn se preferir o ID
                                ->whereNotNull('frt.resposta_tratada')
                                ->where('fe.invalido', false)
                                ->select('frt.resposta_tratada as bairro', DB::raw('COUNT(*) as total'))
                                ->groupBy('frt.resposta_tratada')
                                ->orderByDesc('total')
                                ->get();

        return view('dashboard', compact(
            'envios',
            'totalEnvios',
            'totalQuestionarios',
            'totalBairros',
            'totalUsuarios',
            'enviosRecentes',
            'dadosPorBairro'
        ));
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
