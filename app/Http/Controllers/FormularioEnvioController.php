<?php
namespace App\Http\Controllers;

use App\Models\FormularioEnvio;
use App\Models\FormularioRespostaTratada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormularioEnvioController extends Controller
{
    public function indexValidados(Request $request)
    {
        $query = FormularioEnvio::with(['formulario', 'usuario'])
        ->withCount(['respostasTratadas as pendentes' => function ($q) {
            $q->where('conferida', false);
        }]);

        // 🔍 FILTRO POR STATUS
        if ($request->filled('status')) {
            if ($request->status === 'pendente') {
                $query->whereHas('respostasTratadas', function ($q) {
                    $q->where('conferida', false);
                })
                ->where('invalido', false);
            } elseif ($request->status === 'validado') {
                $query->whereDoesntHave('respostasTratadas', function ($q) {
                    $q->where('conferida', false);
                });
            } elseif ($request->status === 'invalido') {
                $query->where('invalido', true); // ajuste conforme seu campo
            }
        }

        // 🔍 PESQUISA POR QUESTIONÁRIO OU USUÁRIO
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('formulario', fn($f) => $f->where('titulo', 'like', "%{$search}%"))
                ->orWhereHas('usuario', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $envios = $query->paginate(15);

        return view('formularios.validarEnviosLista', compact('envios'));
    }

    public function analisar($id)
    {
        $envio = FormularioEnvio::with([
            'formulario.passos.perguntas',
            'respostas.respostaTratada',
        ])->findOrFail($id);

        return view('formularios.validarEnviosAnalise', compact('envio'));
    }

    public function store(Request $request, $id)
    {
        // Encontre o envio para garantir que existe
        $envio = FormularioEnvio::with('respostas')->findOrFail($id);

        $dadosTratados = $request->input('respostas_tratadas', []);
        foreach ($dadosTratados as $respostaId => $dados) {
            // Pega os dados do input
            $respostaTratadaTexto = $dados['resposta_tratada'] ?? null;
            $validada = isset($dados['validada']); // checkbox pode vir como 1 ou não vir

            // Verifica se essa resposta pertence a esse envio para segurança
            if (! $envio->respostas->pluck('id')->contains($respostaId)) {
                continue; // pula se não pertence
            }

            // Busca registro de resposta tratada, ou cria novo
            $tratada = FormularioRespostaTratada::firstOrNew([
                'resposta_id' => $respostaId,
            ]);

            // Atualiza os campos
            $tratada->resposta_tratada = $respostaTratadaTexto;
            $tratada->conferida = $validada;

            $tratada->save();
        }

        return redirect()
            ->route('validar.envio', $envio->id)
            ->with('success', 'Análise salva com sucesso!');
    }
}
