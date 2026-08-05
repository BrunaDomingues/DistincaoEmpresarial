<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Resultados por bairro — {{ $segmento->titulo }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $formulario->titulo }}
                    @if ($cidadeSelecionada)
                        · {{ $cidadeSelecionada }}
                    @else
                        · Todas as cidades
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('relatorios.segmentos-bairros.exportar', ['segmento_id' => $segmento->id, 'cidade' => $cidadeSelecionada]) }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition">
                    Exportar Excel
                </a>
                <a href="{{ route('relatorios.segmentos-bairros') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                    ← Outro setor
                </a>
            </div>
        </div>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if ($perguntasEncontradas === 0)
                <p class="rounded border border-amber-200 bg-amber-50 p-4 text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100">
                    Nenhuma pergunta classificável foi encontrada neste setor.
                </p>
            @elseif (count($bairros) === 0)
                <p class="text-gray-600 dark:text-gray-300">
                    Nenhuma resposta tratada com bairro informado foi encontrada para este setor.
                </p>
            @endif

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                @foreach ($bairros as $bairro)
                    <section class="overflow-hidden rounded-lg border bg-white shadow dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/50">
                            <h3 class="font-semibold text-indigo-950 dark:text-indigo-100">{{ $bairro['bairro'] }}</h3>
                            <p class="mt-1 text-xs text-indigo-800 dark:text-indigo-200">
                                {{ $bairro['cidade'] }} · {{ $bairro['total'] }} menção(ões)
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="p-2 text-left">#</th>
                                        <th class="p-2 text-left">Empresa</th>
                                        <th class="p-2 text-center">Menções</th>
                                        <th class="p-2 text-left">Fator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bairro['clusters'] as $posicao => $cluster)
                                        <tr class="border-t dark:border-gray-700">
                                            <td class="p-2 text-gray-500 dark:text-gray-400">{{ $posicao + 1 }}</td>
                                            <td class="p-2 text-gray-900 dark:text-gray-100">
                                                <div class="font-medium">{{ $cluster['canonical'] }}</div>
                                                @if (count($cluster['variants']) > 1)
                                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        Grafias:
                                                        @foreach ($cluster['variants'] as $variante)
                                                            <span class="mr-1 inline-block rounded bg-gray-100 px-1 dark:bg-gray-700">
                                                                {{ $variante['label'] }} ({{ $variante['total'] }})
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if (! empty($cluster['requer_validacao']))
                                                    <p class="mt-2 rounded border border-amber-300 bg-amber-50 px-2 py-1 text-xs text-amber-950 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-100">
                                                        <strong>⚠ Validar agrupamento:</strong> {{ $cluster['aviso_validacao'] }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="p-2 text-center font-medium">{{ $cluster['total'] }}</td>
                                            <td class="p-2 text-gray-700 dark:text-gray-300">
                                                {{ $cluster['fator_exibido'] ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
