<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Relatório de Aplicadores Por Dia') }}
            </h2>
        </div>
    </x-slot>

    <x-relatorio-conteudo>
        <x-relatorio-filtros>
            <div class="relatorio-toolbar">
                <form method="GET" class="relatorio-toolbar-form">
                    <label for="data" class="font-medium text-gray-700 dark:text-gray-300">Filtrar por data:</label>
                    <select name="data" id="data" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach ($datasDisponiveis as $data)
                            <option value="{{ $data }}" @selected(request('data') == $data)>
                                {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('relatorios.aplicadores.exportar', ['data' => request('data')]) }}"
                   class="relatorio-toolbar-btn">
                    Exportar Excel
                </a>
            </div>
        </x-relatorio-filtros>

        <x-relatorio-tabela>
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Data</th>
                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Aplicador</th>
                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Formulários Enviados</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($envios as $envio)
                        <tr class="border-t border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($envio->data)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                {{ $envio->usuario->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                {{ $envio->total }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                Nenhum envio encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4 px-4 pb-4">
                {{ $envios->appends(['data' => request('data')])->links() }}
            </div>
        </x-relatorio-tabela>
    </x-relatorio-conteudo>
</x-app-layout>
