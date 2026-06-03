<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatório de Classificação Final') }}
        </h2>
    </x-slot>

    <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        @foreach($dados as $grupoData)
            <div>
                <h2 class="text-lg font-semibold bg-gray-200 px-2 py-1 rounded">
                    {{ $grupoData['grupo']->titulo }}
                </h2>

                <div class="flex flex-wrap gap-6 mt-4">
                    @foreach($grupoData['perguntas'] as $perguntaData)
                        <div class="w-full md:w-1/2">
                            <div class="border rounded overflow-hidden shadow bg-white dark:bg-gray-800">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th colspan="3" class="p-2 text-left border-b">
                                                {{ $perguntaData['pergunta']->pergunta }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="border-b p-2 text-left">Resposta</th>
                                            <th class="border-b p-2 text-center">Qtd</th>
                                            <th class="border-b p-2 text-left">Fator mais utilizado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($perguntaData['respostas'] as $resposta)
                                            <tr>
                                                <td class="border-b p-2">
                                                    {{ $resposta->resposta ?? '-' }}
                                                </td>
                                                <td class="border-b p-2 text-center">
                                                    {{ $resposta->total ?? '-' }}
                                                </td>
                                                <td class="border-b p-2">
                                                    {{ $resposta->fator_mais_utilizado ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="p-2 font-semibold text-center bg-gray-50 dark:bg-gray-900 border-t">
                                                Total: {{ $perguntaData['respostas']->sum('total') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
