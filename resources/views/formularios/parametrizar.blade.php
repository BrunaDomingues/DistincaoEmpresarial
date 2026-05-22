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
<div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2 text-gray-800 dark:text-white">
            <i class="fas fa-sliders-h text-indigo-600"></i> Parametrizar: {{ $formulario->titulo }}
        </h1>
    </div>

    <!-- Cadastro de Fatores de Satisfação -->
    <div x-data="{ aberto: false }" class="mb-8">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Fatores de Satisfação
                </h2>
                <button @click="aberto = !aberto"
                    class="text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <span x-text="aberto ? 'Recolher' : 'Expandir'"></span>
                </button>
            </div>

            <div x-show="aberto" x-transition>
                <form id="form-adiciona-fator">
                    @csrf
                    <input type="hidden" name="formulario_id" value="{{ $formulario->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <input type="text" name="titulo" class="..." placeholder="Título do fator" required>
                        <label class="inline-flex items-center text-gray-700 dark:text-gray-200 self-center">
                            <input type="checkbox" name="resposta_obrigatoria" class="mr-2">
                            Resposta obrigatória
                        </label>

                        <label class="inline-flex items-center text-gray-700 dark:text-gray-200 self-center">
                            <input type="checkbox" name="usa_input_extra" class="mr-2">
                            Utiliza input extra
                        </label>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded self-center">
                            Adicionar Fator
                        </button>
                    </div>
                </form>

                <ul id="lista-fatores" class="space-y-2"></ul>
            </div>
        </div>
    </div>

    <!-- Form para adicionar novo grupo/passo -->
    <div class="mb-8 bg-white dark:bg-gray-800 p-4 rounded shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Cadastro de Grupos</h2>
        </div>

        <form x-show="aberto" id="form-adiciona-passo" class="mt-4 space-y-4">
            <input type="hidden" name="formulario_id" value="{{ $formulario->id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block font-medium dark:text-gray-200">Título do grupo</label>
                    <input type="text" name="titulo" class="w-full border p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600" required>
                </div>
                <div class="col-span-1">
                    <label class="block font-medium dark:text-gray-200">Ordem</label>
                    <input type="number" name="ordem" class="w-full border p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600" required>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Adicionar Grupo</button>
            </div>
        </form>
    </div>

    <!-- Listagem de grupos e perguntas -->
    <div id="sortable-passos" class="space-y-6"></div>
    
    <div class="flex justify-center items-center pt-6">
        <a href="{{ route('formularios.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
            Voltar
        </a>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<!-- Adiciona js de parametrizar -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Sortable(document.getElementById('sortable-passos'), {
            animation: 150,
            onEnd: function () {
                const ids = [...document.querySelectorAll('#sortable-passos > [data-id]')].map(el => el.dataset.id);
                fetch("{{ route('formulario-passos.ordenar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ordens: ids })
                });
            }
        });     
    });

    const formFator = document.getElementById('form-adiciona-fator');
        formFator.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(formFator);
            const url = "{{ route('formulario-fatores.store') }}";

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Fator adicionado com sucesso!',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    carregarFatores();

                    formFator.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao adicionar',
                        text: data.message || 'Tente novamente.',
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro inesperado',
                    text: 'Não foi possível enviar o formulário.',
                });
            }
        });

/* Exclusão de fatores */
document.getElementById('lista-fatores').addEventListener('click', async (e) => {
    // EXCLUIR
    if (e.target.closest('.btn-excluir-fator')) {
        const btn = e.target.closest('.btn-excluir-fator');
        const id = btn.dataset.id;

        const confirm = await Swal.fire({
            title: 'Remover fator?',
            text: 'Tem certeza que deseja excluir este fator?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
            const response = await fetch(`/formulario-fatores/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'DELETE'
                }
            });

            if (response.ok) {
                btn.closest('li').remove();
                Swal.fire({ icon: 'success', title: 'Excluído!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro ao excluir' });
            }
        }

        return; // evita cair no bloco de editar
    }

    // EDITAR
    if (e.target.closest('.btn-editar-fator')) {
        const btn = e.target.closest('.btn-editar-fator');
        const id = btn.dataset.id;

        // Aqui você pode, por exemplo, abrir um modal ou trocar o conteúdo inline para edição
        console.log('Editar fator com ID:', id);

        // Exemplo simples: buscar dados do fator
        try {
            const res = await fetch(`/formulario-fatores/${id}`);
            if (!res.ok) throw new Error('Erro ao buscar fator');

            const fator = await res.json();

            // Agora você pode usar os dados para abrir um modal de edição ou preencher um formulário inline
            Swal.fire({
                title: 'Editar Fator',
                html: `
                    <input type="text" id="titulo-edicao" class="swal2-input" placeholder="Título" value="${fator.titulo}">
                    <label style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
                        <input type="checkbox" id="resposta-obrigatoria-edicao" ${fator.resposta_obrigatoria ? 'checked' : ''}>
                        <span style="margin-left:8px;">Resposta obrigatória</span>
                    </label>
                    <label style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
                        <input type="checkbox" id="utiliza-input-extra-edicao" ${fator.usa_input_extra ? 'checked' : ''}>
                        <span style="margin-left:8px;">Utiliza input extra</span>
                    </label>
                `,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                preConfirm: async () => {
                    const titulo = document.getElementById('titulo-edicao').value;
                    const obrigatorio = document.getElementById('resposta-obrigatoria-edicao').checked;
                    const inputExtra = document.getElementById('utiliza-input-extra-edicao').checked;

                    const updateResponse = await fetch(`/formulario-fatores/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: JSON.stringify({
                            titulo: titulo,
                            resposta_obrigatoria: obrigatorio,
                            usa_input_extra: inputExtra,
                            formulario_id: fator.formulario_id
                        })
                    });

                    if (!updateResponse.ok) {
                        throw new Error('Erro ao salvar');
                    }

                    return updateResponse.json();
                }
            }).then(result => {
                if (result.isConfirmed) {
                    carregarFatores();
                    Swal.fire({ icon: 'success', title: 'Fator atualizado!', timer: 1500, showConfirmButton: false });
                    // aqui você pode atualizar o DOM se quiser
                }
            });

        } catch (err) {
            Swal.fire({ icon: 'error', title: err.message });
        }
    }
});

/* Fim da exclusão de fatores */

/* Busca os fatores para popular */
const carregarFatores = () => {
    const formularioId = document.querySelector('[name="formulario_id"]').value;

    // Realizando a requisição com fetch
    fetch(`/formulario-fatores?formulario_id=${formularioId}`)
        .then(response => response.json())  // Converte a resposta para JSON
        .then(fatores => {
            // Verificando se existem fatores cadastrados
            document.getElementById('lista-fatores').innerHTML = ''; // Limpa a lista antes de adicionar novos fatores
            if (fatores.length > 0) {
                fatores.forEach(fator => {
                    // Criando o item da lista
                    const li = document.createElement('li');
                    li.className = 'bg-gray-50 dark:bg-gray-800 p-3 rounded border dark:border-gray-600 mb-2';

                    // Montando o HTML do fator
                    li.innerHTML = `
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 flex-1 w-full sm:w-auto">
                                <span class="titulo-fator text-gray-800 dark:text-white text-base text-left block">
                                    ${fator.titulo}
                                </span>
                                ${fator.resposta_obrigatoria ? `
                                    <span class="obrigatorio-fator text-sm text-gray-500 dark:text-gray-300 text-left block">
                                        (Resposta obrigatória)
                                    </span>` : ''
                                }
                                ${fator.usa_input_extra ? `
                                    <span class="text-sm text-gray-500 dark:text-gray-300 text-left block">
                                        (Utiliza input extra)
                                    </span>` : ''
                                }
                            </div>

                            <div class="flex gap-2 items-center">
                                <button type="button"
                                    class="btn-editar-fator text-blue-600 hover:text-blue-800"
                                    title="Editar" data-id="${fator.id}">
                                    <i class='bx bx-pencil text-xl'></i>
                                </button>
                                <button type="button"
                                    class="btn-excluir-fator text-red-600 hover:text-red-800"
                                    title="Excluir" data-id="${fator.id}">
                                    <i class='bx bx-trash text-xl'></i>
                                </button>
                            </div>
                        </div>
                    `;

                    // Adiciona o novo item à lista
                    document.getElementById('lista-fatores').appendChild(li);
                });
            } else {
                // Caso não haja fatores, mostrar uma mensagem
                const mensagem = document.createElement('li');
                mensagem.className = 'p-3 text-gray-500 dark:text-gray-300 text-center';
                mensagem.textContent = 'Nenhum fator cadastrado.';
                document.getElementById('lista-fatores').appendChild(mensagem);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar os fatores:', error);
            // Exibe mensagem de erro caso haja um problema
            const mensagem = document.createElement('li');
            mensagem.className = 'p-3 text-red-500 dark:text-red-300 text-center';
            mensagem.textContent = 'Erro ao carregar os fatores. Tente novamente mais tarde.';
            document.getElementById('lista-fatores').appendChild(mensagem);
        });
};

// Chama a função para carregar os fatores assim que a página carregar
carregarFatores();

/* Edição dos fatores */
// Adiciona o evento de clique ao botão de editar
document.querySelectorAll('.btn-editar-fator').forEach(button => {
    button.addEventListener('click', (event) => {
        const fatorId = event.target.dataset.id;
        
        // Cria o formulário de edição
        const titulo = prompt('Digite o novo título do fator:');
        const respostaObrigatoria = confirm('É resposta obrigatória?');

        if (titulo !== null) {
            // Envia os dados para o backend via fetch
            fetch(`/formulario-fatores/${fatorId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    titulo: titulo,
                    resposta_obrigatoria: respostaObrigatoria,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fator atualizado com sucesso');
                    // Atualiza o conteúdo na tela
                    const fatorElement = document.querySelector(`[data-id="${fatorId}"]`).closest('li');
                    fatorElement.querySelector('.titulo-fator').textContent = data.data.titulo;
                    fatorElement.querySelector('.obrigatorio-fator').textContent = data.data.resposta_obrigatoria ? 'Resposta obrigatória' : '';
                } else {
                    alert('Erro ao atualizar o fator');
                }
            })
            .catch(error => {
                console.error('Erro ao editar o fator:', error);
                alert('Erro ao editar o fator');
            });
        }
    });
});

/* grupos/passos */
document.querySelector('#form-adiciona-passo').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {

        fetch('/formulario-passos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Grupo adicionado com sucesso!',
                    timer: 1500,
                    showConfirmButton: false
                });

                form.reset();
                carregarPassos(); // Atualiza a lista de passos */
            } else {
                throw new Error(data.message || 'Erro ao adicionar o passo');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: error.message
        });
    }
});

async function carregarPassos() {
    const resposta = await fetch(`/formulario-passos?formulario_id={{ $formulario->id }}`);
    const passos = await resposta.json();

    const container = document.querySelector('#sortable-passos');
    container.innerHTML = '';

    passos.forEach(passo => {
        const passoDiv = document.createElement('div');
        passoDiv.className = 'border rounded p-4 bg-white dark:bg-gray-800 dark:border-gray-600';
        passoDiv.setAttribute('x-data', '{ aberto: false }');
        passoDiv.setAttribute('data-id', passo.id);

        passoDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold dark:text-white">Grupo: ${passo.titulo}</h2>
                <div class="flex items-center gap-2">
                    <button @click="aberto = !aberto"
                        class="text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        <span x-text="aberto ? 'Recolher' : 'Expandir'"></span>
                    </button>
                   <button type="button"
                        class="btn-excluir-passo text-red-600 hover:text-red-800"
                        title="Excluir grupo"
                        data-id="${passo.id}">
                        <i class="bx bx-trash text-xl"></i>
                    </button>
                </div>
            </div>

            <div x-show="aberto" x-transition>
                <form onsubmit="return adicionarPergunta(event, ${passo.id})" class="mb-4" id="formulario-pergunta-${passo.id}">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="passo_id" value="${passo.id}">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" name="pergunta" placeholder="Pergunta"
                            class="border p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600" required>
                        <select name="tipo"
                            class="border p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600" required>
                            <option value="">Selecione um item</option>
                            <option value="texto">Texto</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="select">Select</option>
                        </select>
                        <label class="inline-flex items-center text-gray-700 dark:text-gray-200">
                            <input type="checkbox" name="obrigatorio" class="mr-2"> Obrigatório
                        </label>
                        <label class="inline-flex items-center text-gray-700 dark:text-gray-200">
                            <input type="checkbox" name="usa_fatores" class="mr-2"> Usar fatores de satisfação
                        </label>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Adicionar Pergunta</button>
                    </div>
                </form>


                <ul class="space-y-2" id="sortable-perguntas-${passo.id}"></ul>
            </div>
        `;

        container.appendChild(passoDiv);

        // Chama a função que carrega as perguntas dinamicamente
        carregarPerguntas(passo.id);
    });
}

carregarPassos();

document.addEventListener('click', async (e) => {
    const btnExcluirPasso = e.target.closest('.btn-excluir-passo');

    if (btnExcluirPasso) {
        const id = btnExcluirPasso.dataset.id;

        const confirm = await Swal.fire({
            title: 'Remover grupo?',
            text: 'Tem certeza que deseja excluir este grupo e todas as suas perguntas?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
            const response = await fetch(`/formulario-passos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                // Remove o grupo da tela (exemplo: remove o <div> que contém o grupo)
                carregarPassos();
                Swal.fire({ icon: 'success', title: 'Grupo excluído!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro ao excluir o grupo' });
            }
        }

        return;
    }
});

/* Perguntas */
async function adicionarPergunta(event, passoId) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    const form = document.getElementById(`formulario-pergunta-${passoId}`);
    const formData = new FormData(form);
    
    try {
        // Enviar os dados via AJAX (fetch)
        const resposta = await fetch(`/formulario-perguntas`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Não se esqueça de incluir o CSRF
            },
            body: formData
        });
        
        if (resposta.ok) {
            // Recarregar todos os itens após a adição
            form.reset();
            carregarPerguntas(passoId);
        } else {
                throw new Error(data.message || 'Erro ao adicionar o pergunta');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: error.message
        });
    }
}

async function carregarPerguntas(passoId) {
    try {
        const resposta = await fetch(`/formulario-perguntas?passo_id=${passoId}`);
        const perguntas = await resposta.json();

        if (!Array.isArray(perguntas)) {
            console.error('A resposta não é um array:', perguntas);
            alert('Erro ao carregar perguntas. Tente novamente.');
            return;
        }

        const container = document.querySelector(`#sortable-perguntas-${passoId}`);
        container.innerHTML = '';

        perguntas.forEach(pergunta => {
            const item = document.createElement('li');
            item.classList.add('bg-gray-50', 'dark:bg-gray-800', 'p-2', 'rounded', 'border', 'dark:border-gray-600');
            item.setAttribute('data-id', pergunta.id);
            item.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <strong class="dark:text-white">${pergunta.pergunta}</strong>
                        <span class="text-sm text-gray-500">(${pergunta.tipo})</span>
                        ${pergunta.obrigatorio ? `
                                    <span class="obrigatorio-fator text-sm text-gray-500 dark:text-gray-300 text-left block">
                                        * Resposta obrigatória
                                    </span>` : ''
                                }
                        ${pergunta.usa_fatores_satisfacao ? `
                                    <span class="obrigatorio-fator text-sm text-gray-500 dark:text-gray-300 text-left block">
                                        * Utiliza fatores de satisfação
                                    </span>` : ''
                                }
                    </div>
                    <div class="flex gap-2 items-center">
                        <button type="button"
                            class="btn-editar-pergunta text-blue-600 hover:text-blue-800"
                            title="Editar"
                            data-id="${pergunta.id}"
                            data-passo-id="${passoId}">
                            <i class="bx bx-pencil text-xl"></i>
                        </button>
                        <button type="button"
                            class="btn-excluir-pergunta text-red-600 hover:text-red-800"
                            title="Excluir"
                            data-id="${pergunta.id}"
                            data-passo-id="${passoId}">
                            <i class="bx bx-trash text-xl"></i>
                        </button>
                    </div>
                </div>
            `;

            // Se a pergunta for tipo múltipla escolha, renderiza opções
            if (['radio', 'checkbox', 'select'].includes(pergunta.tipo)) {
                    carregarOpcoes(pergunta, item, passoId);
            }

            container.appendChild(item);
        });

    } catch (error) {
        console.error('Erro ao carregar perguntas:', error);
        alert('Erro ao carregar perguntas. Tente novamente.');
    }
}

document.addEventListener('click', async (e) => {
    const btnExcluir = e.target.closest('.btn-excluir-pergunta');
    const btnEditar = e.target.closest('.btn-editar-pergunta');

    // EXCLUIR PERGUNTA
    if (btnExcluir) {
        const id = btnExcluir.dataset.id;
        const passoId = btnExcluir.dataset.passoId;

        const confirm = await Swal.fire({
            title: 'Remover pergunta?',
            text: 'Tem certeza que deseja excluir esta pergunta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });

        if (confirm.isConfirmed) {
            const response = await fetch(`/formulario-perguntas/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'DELETE'
                }
            });

            if (response.ok) {
                btnExcluir.closest('li').remove();
                Swal.fire({ icon: 'success', title: 'Pergunta excluída!', timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro ao excluir a pergunta' });
            }
        }

        return;
    }

    // EDITAR PERGUNTA
    if (btnEditar) {
        const id = btnEditar.dataset.id;
        const passoId = btnEditar.dataset.passoId;

        try {
            const res = await fetch(`/formulario-perguntas/${id}`);
            if (!res.ok) throw new Error('Erro ao buscar pergunta');

            const pergunta = await res.json();
            Swal.fire({
                title: 'Editar Pergunta',
                html: `
                    <input type="text" id="pergunta-edicao" class="swal2-input" placeholder="Pergunta" value="${pergunta.pergunta}">
                    <select id="tipo-edicao" class="swal2-select">
                        <option value="texto" ${pergunta.tipo === 'text' ? 'selected' : ''}>Texto</option>
                        <option value="radio" ${pergunta.tipo === 'radio' ? 'selected' : ''}>Radio</option>
                        <option value="checkbox" ${pergunta.tipo === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                        <option value="select" ${pergunta.tipo === 'select' ? 'selected' : ''}>Select</option>
                    </select>
                    <label style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
                        <input type="checkbox" id="obrigatoria-edicao" ${pergunta.obrigatorio ? 'checked' : ''}>
                        <span style="margin-left:8px; font-size: medium;">Resposta obrigatória</span>
                    </label>
                    <label style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
                        <input type="checkbox" id="usa-fatores-edicao" ${pergunta.usa_fatores_satisfacao ? 'checked' : ''}>
                        <span style="margin-left:8px; font-size: medium;">Usa fatores de satisfação</span>
                    </label>
                `,
                showCancelButton: true,
                confirmButtonText: 'Salvar',
                cancelButtonText: 'Cancelar',
                preConfirm: async () => {
                    const texto = document.getElementById('pergunta-edicao').value;
                    const tipo = document.getElementById('tipo-edicao').value;
                    const obrigatoria = document.getElementById('obrigatoria-edicao').checked;
                    const usa_fatores = document.getElementById('usa-fatores-edicao').checked;

                    const updateResponse = await fetch(`/formulario-perguntas/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            pergunta: texto,
                            tipo: tipo,
                            resposta_obrigatoria: obrigatoria,
                            usa_fatores: usa_fatores,
                            passo_id: passoId
                        })
                    });


                    if (!updateResponse.ok) throw new Error('Erro ao salvar');
                    // Atualiza a pergunta no DOM
                    carregarPerguntas(passoId);
                    return updateResponse.json();
                }
            }).then(result => {
                if (result.isConfirmed) {
                    carregarPerguntas(passoId);
                    Swal.fire({ icon: 'success', title: 'Pergunta atualizada!', timer: 1500, showConfirmButton: false });
                }
            });

        } catch (err) {
            Swal.fire({ icon: 'error', title: err.message });
        }
    }
});

/* Opções */
function carregarOpcoes(pergunta, container, passoId) {
    const opcoesContainer = document.createElement('ul');
    opcoesContainer.classList.add('ml-6', 'list-disc');
    opcoesContainer.id = `sortable-opcoes-${pergunta.id}`;

    pergunta.opcoes.forEach(opcao => {
        const opcaoItem = document.createElement('li');
        opcaoItem.classList.add('flex', 'items-center', 'justify-between', 'gap-2');
        opcaoItem.setAttribute('data-id', opcao.id);

        opcaoItem.innerHTML = `
            <span class="dark:text-white">${opcao.opcao}</span>
            <div class="flex items-center gap-1">
                <button type="button" class="btn-editar-opcao text-blue-600 hover:text-blue-800" title="Editar"
                    data-id="${opcao.id}" data-passo-id="${passoId}" data-pergunta-id="${pergunta.id}" data-opcao="${opcao.opcao}">
                    <i class='bx bx-pencil text-sm'></i>
                </button>
                <button type="button" class="btn-excluir-opcao text-red-600 hover:text-red-800" title="Excluir"
                    data-id="${opcao.id}" data-passo-id="${passoId}" data-pergunta-id="${pergunta.id}">
                    <i class='bx bx-trash text-sm'></i>
                </button>
            </div>
        `;
        opcoesContainer.appendChild(opcaoItem);
    });

    const formularioAdicionarOpcao = document.createElement('form');
    formularioAdicionarOpcao.onsubmit = (e) => adicionarOpcao(e, pergunta.id);
    formularioAdicionarOpcao.classList.add('mt-2');

    formularioAdicionarOpcao.innerHTML = `
        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
        <input type="hidden" name="pergunta_id" value="${pergunta.id}">
        <input type="hidden" name="passo_id" value="${passoId}">
        <input type="text" name="opcao" placeholder="Nova opção"
            class="border p-2 mr-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600" required>
        <button type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 rounded">Adicionar Opção</button>
    `;

    container.appendChild(opcoesContainer);
    container.appendChild(formularioAdicionarOpcao);
}

async function adicionarOpcao(e, perguntaId) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('/formulario-opcoes', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData,
        });

        const resultado = await response.json();

        if (resultado.success) {
            Swal.fire({
                icon: 'success',
                title: 'Opção adicionada com sucesso!',
                timer: 1500,
                showConfirmButton: false
            });
            const passoId = form.querySelector('[name="passo_id"]').value;
            carregarPerguntas(passoId);

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao adicionar opção',
                text: resultado.message || 'Tente novamente.'
            });
        }
    } catch (error) {
        console.error('Erro ao adicionar opção:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro inesperado',
            text: 'Tente novamente.'
        });
    }
}

async function editarOpcao(id, perguntaId, textoAtual, passoId) {
    const { value: novoTexto } = await Swal.fire({
        title: 'Editar Opção',
        input: 'text',
        inputValue: textoAtual,
        inputPlaceholder: 'Digite a nova opção',
        showCancelButton: true,
        confirmButtonText: 'Salvar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'O texto da opção não pode estar vazio!';
            }
        }
    });

    if (novoTexto) {
        try {
            const response = await fetch(`/formulario-opcoes/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ opcao: novoTexto })
            });

            const resultado = await response.json();

            if (resultado.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Opção atualizada!',
                    timer: 1500,
                    showConfirmButton: false
                });

                carregarPerguntas(passoId);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao atualizar',
                    text: resultado.message || 'Tente novamente.'
                });
            }
        } catch (error) {
            console.error('Erro ao editar opção:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro inesperado',
                text: 'Tente novamente.'
            });
        }
    }
}

function excluirOpcao(id, perguntaId, passoId) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa opção será removida permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/formulario-opcoes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const resultado = await response.json();

                if (resultado.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Opção excluída!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                     carregarPerguntas(passoId);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao excluir',
                        text: resultado.message || 'Tente novamente.'
                    });
                }
            } catch (error) {
                console.error('Erro ao excluir opção:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro inesperado',
                    text: 'Tente novamente.'
                });
            }
        }
    });
}

document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-editar-opcao')) {
        const btn = e.target.closest('.btn-editar-opcao');
        const id = btn.dataset.id;
        const perguntaId = btn.dataset.perguntaId;
        const texto = btn.dataset.opcao;
        const passoId = btn.dataset.passoId;
        editarOpcao(id, perguntaId, texto, passoId);
    }

    if (e.target.closest('.btn-excluir-opcao')) {
        const btn = e.target.closest('.btn-excluir-opcao');
        const id = btn.dataset.id;
        const perguntaId = btn.dataset.perguntaId;
        const passoId = btn.dataset.passoId;
        excluirOpcao(id, perguntaId, passoId);
    }
});
</script>
</x-app-layout>
