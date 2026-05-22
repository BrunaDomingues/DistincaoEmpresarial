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

        // Dados agrupados por bairro (geolocalização do envio)
        $dadosPorBairro = DB::table('formulario_envios')
            ->where('invalido', false)
            ->whereNotNull('bairro')
            ->whereRaw("TRIM(bairro) <> ''")
            ->select(DB::raw('TRIM(bairro) as bairro'), DB::raw('COUNT(id) as total'))
            ->groupBy(DB::raw('TRIM(bairro)'))
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
