<?php

namespace App\Http\Controllers;

use App\Models\InsightEmpresaAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsightEmpresaAliasController extends Controller
{
    public function index()
    {
        $aliases = InsightEmpresaAlias::query()
            ->orderBy('nome_canonico')
            ->orderBy('termo')
            ->paginate(30);

        return view('insight.empresa_aliases.index', compact('aliases'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        InsightEmpresaAlias::create([
            ...$data,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('insight.empresa-aliases.index')
            ->with('success', 'Correspondência salva com sucesso.');
    }

    public function update(Request $request, InsightEmpresaAlias $alias)
    {
        $data = $this->validated($request, $alias->id);

        $alias->update([
            ...$data,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('insight.empresa-aliases.index')
            ->with('success', 'Correspondência atualizada com sucesso.');
    }

    public function destroy(InsightEmpresaAlias $alias)
    {
        $alias->delete();

        return redirect()
            ->route('insight.empresa-aliases.index')
            ->with('success', 'Correspondência removida.');
    }

    /** @return array{termo: string, termo_normalizado: string, nome_canonico: string} */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $request->validate([
            'termo' => 'required|string|max:255',
            'nome_canonico' => 'required|string|max:255',
        ]);

        $termo = trim($request->input('termo'));
        $nomeCanonico = trim($request->input('nome_canonico'));
        $termoNormalizado = InsightEmpresaAlias::normalizarTermo($termo);

        $request->validate([
            'termo' => [
                function ($attribute, $value, $fail) use ($termoNormalizado, $ignoreId) {
                    if ($termoNormalizado === '') {
                        $fail('Informe um termo válido para correspondência.');

                        return;
                    }

                    $exists = InsightEmpresaAlias::query()
                        ->where('termo_normalizado', $termoNormalizado)
                        ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma correspondência para este termo.');
                    }
                },
            ],
        ]);

        return [
            'termo' => $termo,
            'termo_normalizado' => $termoNormalizado,
            'nome_canonico' => $nomeCanonico,
        ];
    }
}
