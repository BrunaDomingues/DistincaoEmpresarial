<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Relatório de Respondentes por Bairro') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtro + Exportar --}}
            <div class="mb-4 flex items-center justify-between">
                {{-- 
                <form method="GET" class="flex items-center gap-2">
                    <label for="data" class="font-medium text-gray-700 dark:text-gray-300">Filtrar por data:</label>
                    <select name="data" id="data" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded px-2 py-1" style="min-width: 160px;">
                        <option value="">Todas</option>
                        @foreach ($datasDisponiveis as $data)
                            <option value="{{ $data }}" @selected(request('data') == $data)>
                                {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </form> 
                --}}

                <a href="{{ route('relatorios.bairros.export', ['data' => request('data')]) }}"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">
                    Exportar Excel
                </a>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Bairro</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Total de Respondentes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bairros as $bairro)
                            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                    {{ $bairro->bairro }}
                                </td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                    {{ $bairro->total_respondentes }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    Nenhum dado encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginação --}}
            <div class="mt-4">
                {{ $bairros->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
