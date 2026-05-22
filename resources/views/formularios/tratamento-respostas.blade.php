<x-app-layout>
    <style>
    .swal2-select {
        width: 80%;
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
        font-size: medium !important;
    }

    .swal2-input {
        width: 80%;
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
        font-size: medium !important;
    }
    </style>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Respostas Tratadas') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('respostas-tratadas.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Formulário -->
                    <div>
                        <label for="formulario_id" class="block text-sm font-medium text-gray-700">Formulário</label>
                        <select name="formulario_id" id="formulario_id" class="mt-1 block w-full border rounded p-2">
                            <option value="">Todos</option>
                            @foreach($formularios as $form)
                            <option value="{{ $form->id }}" {{ request('formulario_id') == $form->id ? 'selected' : '' }}>
                                {{ $form->titulo }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Conferida -->
                    <div>
                        <label for="conferida" class="block text-sm font-medium text-gray-700">Conferida?</label>
                        <select name="conferida" id="conferida" class="mt-1 block w-full border rounded p-2">
                            <option value="">Todas</option>
                            <option value="1" {{ request('conferida') === '1' ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ request('conferida') === '0' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <!-- Filtro por Pergunta -->
                    <div>
                        <label for="pergunta" class="block text-sm font-medium text-gray-700">Pergunta</label>
                        <input type="text" name="pergunta" id="pergunta" class="mt-1 block w-full border rounded p-2"
                            placeholder="Digite parte da pergunta..."
                            value="{{ request('pergunta') }}">
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filtrar</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto sm:overflow-visible bg-white dark:bg-gray-900 shadow ">
                <table class="w-full table-auto text-sm border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Resposta ID</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Formulário</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Passo/Grupo</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Pergunta</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Resposta Original</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Resposta Tratada</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Conferida</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($respostas)) 
                            <tr>
                                <td colspan="8" class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    Nenhuma resposta tratada encontrada.
                                </td>
                            </tr>
                        @else
                            @foreach($respostas as $resposta)
                                <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100 text-center">
                                        {{ $resposta->id }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->pergunta->passo->formulario->titulo ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->pergunta->passo->titulo ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->pergunta->pergunta ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->resposta }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->respostaTratada->resposta_tratada ?? '' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-800 dark:text-gray-100">
                                        {{ $resposta->respostaTratada->conferida ? 'Sim' : 'Não' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <a 
                                            class="p-2 rounded-full text-blue-500 transition-transform transform hover:scale-110"
                                            onclick="abrirEdicao({{ $resposta->respostaTratada->id ?? 'null' }})"
                                            {{ $resposta->respostaTratada ? '' : 'disabled' }}
                                        > 
                                            <i class="bx bx-edit text-xl"></i> 
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="mt-6 flex justify-center space-x-2">
                    {{ $respostas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
    async function abrirEdicao(respostaTratadaId) {
        if (!respostaTratadaId) {
            Swal.fire('Erro', 'Resposta tratada não encontrada.', 'error');
            return;
        }

        try {
            const response = await fetch(`/respostas-tratadas/${respostaTratadaId}/dados`);
            if (!response.ok) throw new Error('Erro ao buscar dados');

            const data = await response.json();
            const { pergunta, opcoes, tipo, resposta_id, resposta_tratada, conferida, grupo, formulario } = data;

            let inputHtml = '';

            if (tipo === 'radio') {
                inputHtml = opcoes.map(opcao => `
                    <label class="flex items-center space-x-2 my-1">
                        <input type="radio" name="resposta_tratada" value="${opcao}" ${resposta_tratada === opcao ? 'checked' : ''}>
                        <span>${opcao}</span>
                    </label>
                `).join('');
            } else if (tipo === 'select') {
                inputHtml = `<select name="resposta_tratada" class="swal2-input">` +
                    opcoes.map(opcao => `
                        <option value="${opcao}" ${resposta_tratada === opcao ? 'selected' : ''}>${opcao}</option>
                    `).join('') +
                    `</select>`;
            } else {
                inputHtml = `
                    <input type="text" name="resposta_tratada" class="swal2-input" value="${resposta_tratada || ''}">
                `;
            }

            const htmlContent = `
                <div class="text-left text-sm mb-2">
                    <p><strong>ID:</strong> ${resposta_id}</p>
                    <p><strong>Formulário:</strong> ${formulario}</p>
                    <p><strong>Grupo:</strong> ${grupo}</p>
                    <p><strong>Pergunta:</strong> ${pergunta}</p>
                </div>
                <form id="form-resposta-tratada" class="text-left">
                    ${inputHtml}
                    <label class="flex items-center space-x-2 mt-4">
                        <input type="checkbox" name="conferida" ${conferida ? 'checked' : ''}>
                        <span>Marcar como conferida</span>
                    </label>
                </form>
            `;

            const { isConfirmed, value } = await Swal.fire({
                title: 'Editar Resposta Tratada',
                html: htmlContent,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const form = document.getElementById('form-resposta-tratada');
                    const formData = new FormData(form);
                    return {
                        resposta_tratada: formData.get('resposta_tratada'),
                        conferida: formData.get('conferida') === 'on' ? 1 : 0
                    };
                }
            });

            if (isConfirmed && value) {
                await fetch(`/respostas-tratadas/${respostaTratadaId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(value)
                });

                Swal.fire('Sucesso', 'Resposta tratada salva com sucesso.', 'success')
                    .then(() => location.reload());
            }

        } catch (err) {
            console.error(err);
            Swal.fire('Erro', 'Não foi possível carregar os dados.', 'error');
        }
    }
</script>
</x-app-layout>
