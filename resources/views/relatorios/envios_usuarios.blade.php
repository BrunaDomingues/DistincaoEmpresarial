<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Todos os envios de formulários
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Lista completa com filtros — do mais recente ao mais antigo
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                ← Voltar ao dashboard
            </a>
        </div>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col gap-4 relatorio-acoes">
                <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
                    <div>
                        <label for="formulario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Formulário
                        </label>
                        <select name="formulario_id" id="formulario_id"
                            class="relatorio-filtro-select relatorio-filtro-select--wide border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded">
                            <option value="">Todos</option>
                            @foreach ($formularios as $form)
                                <option value="{{ $form->id }}" @selected(request('formulario_id') == $form->id)>
                                    {{ $form->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="usuario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Usuário
                        </label>
                        <select name="usuario_id" id="usuario_id"
                            class="relatorio-filtro-select relatorio-filtro-select--wide border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded">
                            <option value="">Todos</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected(request('usuario_id') == $usuario->id)>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="data" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dia
                        </label>
                        <select name="data" id="data"
                            class="relatorio-filtro-select border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded">
                            <option value="">Todos os dias</option>
                            @foreach ($datasDisponiveis as $data)
                                <option value="{{ $data }}" @selected(request('data') == $data)>
                                    {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="btn-relatorio bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded transition">
                        Filtrar
                    </button>
                    @if (request()->hasAny(['formulario_id', 'usuario_id', 'data']))
                        <a href="{{ route('relatorios.envios-usuarios') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 py-2">
                            Limpar filtros
                        </a>
                    @endif
                </form>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $totalEnvios }} envio(s) no total
                        @if ($enviosPaginados->hasPages())
                            — página {{ $enviosPaginados->currentPage() }} de {{ $enviosPaginados->lastPage() }}
                        @endif
                    </p>
                    <a href="{{ route('relatorios.envios-usuarios.export', request()->only(['formulario_id', 'data'])) }}"
                        class="btn-relatorio inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition shrink-0 text-center">
                        Exportar Excel
                    </a>
                </div>
            </div>

            @forelse ($enviosPorDia as $blocoDia)
                <section class="overflow-hidden bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <header class="bg-gray-100 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 capitalize">
                            {{ $blocoDia['label'] }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $blocoDia['itens']->count() }} envio(s) neste dia
                        </p>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/80">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200 font-medium">Horário</th>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200 font-medium">Usuário</th>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200 font-medium">Formulário</th>
                                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200 font-medium hidden sm:table-cell">E-mail</th>
                                    <th class="px-4 py-2 text-center text-gray-700 dark:text-gray-200 font-medium">Respostas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blocoDia['itens'] as $item)
                                    @php $envio = $item['envio']; @endphp
                                    <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap font-mono">
                                            {{ $item['horario_curto'] }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                            {{ $envio->usuario->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                            {{ $envio->formulario->titulo ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400 hidden sm:table-cell">
                                            {{ $envio->usuario->email ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('envios.show', $envio) }}"
                                                class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                                Ver respostas
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @empty
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-8 text-center text-gray-500 dark:text-gray-400">
                    Nenhum envio encontrado com os filtros selecionados.
                </div>
            @endforelse

            @if ($enviosPaginados->hasPages())
                <div class="mt-4">
                    {{ $enviosPaginados->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
