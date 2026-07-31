<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Ranking consolidado — {{ $formulario->titulo }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('insight.ranking-empresas.export', ['formulario_id' => $formulario->id]) }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition">
                    Exportar Excel
                </a>
                <a href="{{ route('insight.ranking-empresas') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                    ← Outro questionário
                </a>
            </div>
        </div>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            @if (count($dadosAgrupados) === 0)
                <p class="text-gray-600 dark:text-gray-300">Nenhum segmento com fatores de satisfação encontrado neste formulário.</p>
            @endif

            @foreach ($dadosAgrupados as $grupoData)
                <div>
                    <h2 class="text-lg font-semibold bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-gray-900 dark:text-gray-100">
                        {{ $grupoData['grupo']->titulo }}
                    </h2>

                    <div class="flex flex-wrap gap-6 mt-4">
                        @foreach ($grupoData['perguntas'] as $perguntaData)
                            @php
                                $clusters = $perguntaData['clusters'];
                                $opcoesReconhecimento = $perguntaData['opcoes_reconhecimento'] ?? [];
                                $lider = $clusters[0] ?? null;
                            @endphp
                            <div class="w-full md:w-1/2 space-y-3">
                                <div class="border rounded-lg overflow-hidden shadow bg-white dark:bg-gray-800">
                                    <div class="bg-indigo-50 dark:bg-indigo-950/50 px-3 py-2 border-b border-indigo-100 dark:border-indigo-900">
                                        <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                                            {{ $perguntaData['pergunta']->pergunta }}
                                        </p>
                                        @if ($lider)
                                            <p class="mt-1 text-xs text-indigo-800 dark:text-indigo-200">
                                                <span class="font-semibold">Na frente (consolidado):</span>
                                                {{ $lider['canonical'] }}
                                                — {{ $lider['total'] }} menção(ões)
                                            </p>
                                        @endif
                                    </div>

                                    @if (count($opcoesReconhecimento) > 0)
                                        <div class="border-b dark:border-gray-700">
                                            <div class="bg-slate-50 dark:bg-slate-900/40 px-3 py-2">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">
                                                    Opções sem nome de empresa
                                                </p>
                                            </div>
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-100 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="p-2 text-left border-b dark:border-gray-600">Opção</th>
                                                        <th class="p-2 text-center border-b dark:border-gray-600">Qtd</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($opcoesReconhecimento as $opcao)
                                                        <tr class="dark:border-gray-700">
                                                            <td class="border-b p-2 font-medium text-gray-900 dark:text-gray-100">
                                                                {{ $opcao['canonical'] }}
                                                            </td>
                                                            <td class="border-b p-2 text-center font-medium">
                                                                {{ $opcao['total'] }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="p-2 text-left border-b dark:border-gray-600">#</th>
                                                <th class="p-2 text-left border-b dark:border-gray-600">Nome consolidado</th>
                                                <th class="p-2 text-center border-b dark:border-gray-600">Qtd</th>
                                                <th class="p-2 text-left border-b dark:border-gray-600">Fator</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clusters as $i => $c)
                                                <tr class="dark:border-gray-700">
                                                    <td class="border-b p-2 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                                    <td class="border-b p-2 text-gray-900 dark:text-gray-100">
                                                        <div class="font-medium">{{ $c['canonical'] }}</div>
                                                        @if (count($c['variants']) > 1)
                                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                Grafias no banco:
                                                                @foreach ($c['variants'] as $v)
                                                                    <span class="inline-block mr-1 rounded bg-gray-100 px-1 dark:bg-gray-700">{{ $v['label'] }} ({{ $v['total'] }})</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if (!empty($c['requer_validacao']))
                                                            <div class="mt-2 rounded border border-amber-300 bg-amber-50 px-2 py-1.5 text-xs text-amber-950 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-100">
                                                                <span class="font-semibold">⚠ Validar agrupamento:</span>
                                                                {{ $c['aviso_validacao'] }}
                                                                <a href="{{ route('insight.empresa-aliases.index') }}"
                                                                    class="mt-1 inline-block font-medium text-amber-900 underline dark:text-amber-200">
                                                                    Gerenciar correspondências
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="border-b p-2 text-center font-medium">{{ $c['total'] }}</td>
                                                    <td class="border-b p-2 text-gray-700 dark:text-gray-300">
                                                        {{ $c['fator_exibido'] ?? '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <details class="rounded border border-gray-200 bg-gray-50 text-sm dark:border-gray-600 dark:bg-gray-900/50">
                                    <summary class="cursor-pointer px-3 py-2 font-medium text-gray-700 dark:text-gray-300">
                                        Ver linhas como no relatório de classificação (sem agrupar)
                                    </summary>
                                    <div class="overflow-x-auto border-t dark:border-gray-600">
                                        <table class="w-full text-xs">
                                            <thead class="bg-gray-100 dark:bg-gray-800">
                                                <tr>
                                                    <th class="p-2 text-left">Resposta</th>
                                                    <th class="p-2 text-center">Qtd</th>
                                                    <th class="p-2 text-left">Fator</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($perguntaData['respostas_originais'] as $r)
                                                    <tr>
                                                        <td class="border-t p-2 dark:border-gray-700">{{ $r->resposta ?? '—' }}</td>
                                                        <td class="border-t p-2 text-center dark:border-gray-700">{{ $r->total }}</td>
                                                        <td class="border-t p-2 dark:border-gray-700">{{ $r->fator_mais_utilizado ?? '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </details>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
