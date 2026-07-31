<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Ranking por pesquisador — {{ $formulario->titulo }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Não conheço / Conheço mas não lembro
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('insight.ranking-reconhecimento-pesquisadores.export', ['formulario_id' => $formulario->id]) }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded transition">
                    Exportar Excel
                </a>
                <a href="{{ route('insight.ranking-reconhecimento-pesquisadores') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                    ← Outro questionário
                </a>
            </div>
        </div>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            @if ($fatores->isEmpty())
                <p class="text-gray-600 dark:text-gray-300">
                    Este formulário não possui opções do tipo “Não conheço” / “Conheço mas não lembro”.
                </p>
            @elseif (count($ranking) === 0)
                <p class="text-gray-600 dark:text-gray-300">
                    Nenhum envio válido encontrado para este formulário.
                </p>
            @else
                <div class="border rounded-lg overflow-hidden shadow bg-white dark:bg-gray-800">
                    <div class="bg-indigo-50 dark:bg-indigo-950/50 px-3 py-2 border-b border-indigo-100 dark:border-indigo-900">
                        <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                            Visão geral por pesquisador
                        </p>
                        <p class="mt-1 text-xs text-indigo-800 dark:text-indigo-200">
                            Contagem de todas as respostas com essas opções no questionário (todas as perguntas).
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="p-2 text-left border-b dark:border-gray-600">#</th>
                                    <th class="p-2 text-left border-b dark:border-gray-600">Pesquisador</th>
                                    <th class="p-2 text-center border-b dark:border-gray-600">Envios</th>
                                    @foreach ($fatores as $fator)
                                        <th class="p-2 text-center border-b dark:border-gray-600">{{ $fator->titulo }}</th>
                                    @endforeach
                                    <th class="p-2 text-center border-b dark:border-gray-600">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ranking as $i => $row)
                                    <tr class="dark:border-gray-700">
                                        <td class="border-b p-2 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                        <td class="border-b p-2 font-medium text-gray-900 dark:text-gray-100">{{ $row['nome'] }}</td>
                                        <td class="border-b p-2 text-center">{{ $row['total_envios'] }}</td>
                                        @foreach ($fatores as $fator)
                                            <td class="border-b p-2 text-center">
                                                {{ $row['totais_por_fator'][$fator->id] ?? 0 }}
                                            </td>
                                        @endforeach
                                        <td class="border-b p-2 text-center font-semibold">{{ $row['total_reconhecimento'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (count($porPergunta) > 0)
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Detalhe por pergunta
                        </h3>

                        @foreach ($porPergunta as $bloco)
                            <div class="border rounded-lg overflow-hidden shadow bg-white dark:bg-gray-800">
                                <div class="bg-slate-50 dark:bg-slate-900/40 px-3 py-2 border-b dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $bloco['pergunta'] }}
                                    </p>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="p-2 text-left border-b dark:border-gray-600">#</th>
                                                <th class="p-2 text-left border-b dark:border-gray-600">Pesquisador</th>
                                                @foreach ($fatores as $fator)
                                                    <th class="p-2 text-center border-b dark:border-gray-600">{{ $fator->titulo }}</th>
                                                @endforeach
                                                <th class="p-2 text-center border-b dark:border-gray-600">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bloco['ranking'] as $i => $row)
                                                <tr class="dark:border-gray-700">
                                                    <td class="border-b p-2 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                                    <td class="border-b p-2 font-medium text-gray-900 dark:text-gray-100">{{ $row['nome'] }}</td>
                                                    @foreach ($fatores as $fator)
                                                        <td class="border-b p-2 text-center">
                                                            {{ $row['totais_por_fator'][$fator->id] ?? 0 }}
                                                        </td>
                                                    @endforeach
                                                    <td class="border-b p-2 text-center font-semibold">{{ $row['total_reconhecimento'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
