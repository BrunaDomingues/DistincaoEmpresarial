<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                Correspondências de nomes de empresas
            </h2>
            <a href="{{ route('insight.ranking-empresas') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                ← Voltar ao ranking
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-950 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-100">
                <p class="font-medium">Como funciona</p>
                <p class="mt-1">
                    Cadastre aqui grafias erradas ou variações que devem ser agrupadas sob um mesmo nome no ranking.
                    Exemplo: termo <strong>corradini alimentos</strong> → nome canônico <strong>Coradini</strong>.
                </p>
            </div>

            @if (session('success'))
                <div class="text-green-600 dark:text-green-400">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Nova correspondência</h3>
                <form method="POST" action="{{ route('insight.empresa-aliases.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label for="termo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Termo digitado (variação)
                        </label>
                        <input type="text" name="termo" id="termo" value="{{ old('termo') }}"
                            class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded p-2"
                            placeholder="Ex.: corradini alimentos" required>
                        @error('termo')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nome_canonico" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nome canônico (correto)
                        </label>
                        <input type="text" name="nome_canonico" id="nome_canonico" value="{{ old('nome_canonico') }}"
                            class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded p-2"
                            placeholder="Ex.: Coradini" required>
                        @error('nome_canonico')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Termo</th>
                            <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Nome canônico</th>
                            <th class="px-4 py-2 text-center text-gray-700 dark:text-gray-200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aliases as $alias)
                            <tr class="border-t border-gray-200 dark:border-gray-700 align-top">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">
                                    <form id="form-alias-{{ $alias->id }}" method="POST" action="{{ route('insight.empresa-aliases.update', $alias) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="termo" value="{{ old('termo', $alias->termo) }}"
                                            class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded p-2">
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">
                                    <input type="text" name="nome_canonico" form="form-alias-{{ $alias->id }}"
                                        value="{{ old('nome_canonico', $alias->nome_canonico) }}"
                                        class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded p-2">
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <button type="submit" form="form-alias-{{ $alias->id }}"
                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 px-2 py-1">
                                        Salvar
                                    </button>
                                    <form method="POST" action="{{ route('insight.empresa-aliases.destroy', $alias) }}"
                                        class="inline"
                                        onsubmit="return confirm('Remover esta correspondência?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 px-2 py-1">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Nenhuma correspondência cadastrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $aliases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
