<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Formulários') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
            <div class="text-end">
                <a href="{{ route('formularios.create') }}"
                   class="mb-4 inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">
                    Novo Formulário
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 text-green-600 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow ">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">ID</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Título</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Status</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Data Início</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Data Fim</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Criado por</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($formularios->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    Nenhum formulário encontrado.
                                </td>
                            </tr>
                        @else 
                        @foreach($formularios as $formulario)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $formulario->id }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $formulario->titulo }}</td>
                            <td class="px-4 py-2">
                                @if ($formulario->aceitando_respostas)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        Aberto
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                        Encerrado
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                {{ $formulario->data_inicio ? $formulario->data_inicio->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                {{ $formulario->data_fim ? $formulario->data_fim->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ optional($formulario->criador)->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <!-- Visualizar -->
                                    <button type="button"
                                        class="btn-ver-formulario text-green-400 hover:text-green-500 p-2 rounded-full transition-transform transform hover:scale-110"
                                        data-titulo="{{ e($formulario->titulo) }}"
                                        data-descricao="{{ e($formulario->descricao ?: '-') }}"
                                        data-data-inicio="{{ $formulario->data_inicio ? $formulario->data_inicio->format('d/m/Y') : '-' }}"
                                        data-data-fim="{{ $formulario->data_fim ? $formulario->data_fim->format('d/m/Y') : '-' }}"
                                        data-criador="{{ e(optional($formulario->criador)->name ?? '-') }}"
                                        data-atualizador="{{ e(optional($formulario->editor)->name ?? '-') }}">
                                        <i class="bx bx-show text-xl"></i>
                                    </button>

                                    <!-- Editar -->
                                    <a href="{{ route('formularios.edit', $formulario) }}"
                                    class="p-2 rounded-full text-blue-500 transition-transform transform hover:scale-110">
                                        <i class="bx bx-edit text-xl"></i>
                                    </a>

                                    <!-- Parametrizar -->
                                    <a href="{{ route('formularios.parametrizar', $formulario) }}"
                                    class="text-indigo-600 p-2 rounded-full transition-transform transform hover:scale-110">
                                        <i class="bx bx-cog text-xl"></i>
                                    </a>

                                    <!-- Encerrar / Reabrir -->
                                    <form action="{{ route('formularios.toggle-aceitando-respostas', $formulario) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                title="{{ $formulario->aceitando_respostas ? 'Encerrar formulário' : 'Reabrir formulário' }}"
                                                onclick="return confirm('{{ $formulario->aceitando_respostas ? 'Encerrar este formulário? Ele deixará de aceitar novas respostas.' : 'Reabrir este formulário para voltar a aceitar respostas?' }}')"
                                                class="p-2 rounded-full transition-transform transform hover:scale-110 {{ $formulario->aceitando_respostas ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600' }}">
                                            <i class="bx {{ $formulario->aceitando_respostas ? 'bx-lock-alt' : 'bx-lock-open-alt' }} text-xl"></i>
                                        </button>
                                    </form>

                                    <!-- Excluir -->
                                    <form action="{{ route('formularios.destroy', $formulario) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Tem certeza?')"
                                                class="p-2 rounded-full text-red-500 transition-transform transform hover:scale-110">
                                            <i class="bx bx-trash text-xl"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Modal de visualização --}}
    
<x-modal name="view-form-formulario" :show="false">    
    <div class="space-y-4 p-6">
        <!-- Título -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Título:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="titulo"></p>
        </div>

        <!-- Descrição -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Descrição:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="descricao"></p>
        </div>

        <!-- Data Início -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Data Início:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="data-inicio"></p>
        </div>

        <!-- Data Fim -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Data Fim:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="data-fim"></p>
        </div>

        <!-- Criado por -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Criado por:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="criador"></p>
        </div>

        <!-- Última Edição -->
        <div class="flex justify-between">
            <p class="font-medium text-gray-600 dark:text-gray-300">{{ __('Última edição:') }}</p>
            <p class="text-gray-900 dark:text-gray-200" id="atualizador"></p>
        </div>
    </div>
</x-modal>



{{-- Scripts para controle do modal --}}
<script>
function openViewModal(data) {
    document.getElementById('titulo').textContent = data.titulo;
    document.getElementById('descricao').textContent = data.descricao;
    document.getElementById('data-inicio').textContent = data.data_inicio;
    document.getElementById('data-fim').textContent = data.data_fim;
    document.getElementById('criador').textContent = data.criador;
    document.getElementById('atualizador').textContent = data.atualizador;
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'view-form-formulario' }));
}

document.querySelectorAll('.btn-ver-formulario').forEach(function (btn) {
    btn.addEventListener('click', function () {
        openViewModal({
            titulo: btn.dataset.titulo,
            descricao: btn.dataset.descricao,
            data_inicio: btn.dataset.dataInicio,
            data_fim: btn.dataset.dataFim,
            criador: btn.dataset.criador,
            atualizador: btn.dataset.atualizador,
        });
    });
});
</script>
</x-app-layout>
