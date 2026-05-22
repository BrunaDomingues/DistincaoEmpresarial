<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Validação de Envios</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <form method="GET" action="{{ route('validar-envios.index') }}" class="mb-4 flex flex-wrap gap-2">
                {{-- Campo de pesquisa --}}
                <input type="text" name="search" placeholder="Pesquisar por questionário ou usuário"
                    value="{{ request('search') }}"
                    class="border rounded px-3 py-2 w-1/2 dark:bg-gray-700 dark:text-white" />

                {{-- Filtro de status --}}
                <select name="status" class="border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos os status</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="validado" {{ request('status') == 'validado' ? 'selected' : '' }}>Validado</option>
                    <option value="invalido" {{ request('status') == 'invalido' ? 'selected' : '' }}>Inválido</option>
                </select>

                {{-- Botão --}}
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Filtrar
                </button>
            </form>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">ID</th>
                        <th class="py-2">Questionário</th>
                        <th class="py-2">Usuário</th>
                        <th class="py-2">Data</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($envios as $envio)
                        <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="py-2">{{ $envio->id }}</td>
                            <td class="py-2">{{ $envio->formulario->titulo }}</td>
                            <td class="py-2">{{ $envio->usuario->name ?? 'N/A' }}</td>
                            <td class="py-2">{{ $envio->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2">
                                @php
                                    $status = $envio->status_validacao; // ou atributo calculado
                                @endphp
                                <span class="px-2 py-1 rounded text-sm
                                    @if ($status === 'Válido') bg-green-200 text-green-800
                                    @elseif ($status === 'Pendente') bg-yellow-200 text-yellow-800
                                    @else bg-red-200 text-red-800 @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="py-2 text-right">
                                <a href="{{ route('validar.envio', $envio->id) }}"
                                   class="text-blue-600 hover:underline">Analisar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $envios->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
