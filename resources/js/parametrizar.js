    document.addEventListener('DOMContentLoaded', () => {
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

                    // Adicionar novo fator na lista (poderia ser um HTML retornado ou apenas renderização básica)
                    const novoItem = document.createElement('li');
                    novoItem.className = 'bg-gray-50 dark:bg-gray-800 p-3 rounded border dark:border-gray-600';
                    novoItem.innerHTML = `
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <form method="POST" action="/formulario-fatores/${data.id}" class="flex flex-wrap items-center gap-2 flex-1">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="formulario_id" value="${data.formulario_id}">

                                <input type="text" name="titulo" value="${data.titulo}"
                                    class="border p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600 rounded flex-grow min-w-[200px]"
                                    required>

                                <label class="inline-flex items-center text-gray-700 dark:text-gray-200 whitespace-nowrap">
                                    <input type="checkbox" name="resposta_obrigatoria" class="mr-2">
                                    Resposta obrigatória
                                </label>

                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                                    Salvar
                                </button>
                            </form>

                            <button type="button" class="btn-excluir-fator text-red-600 text-xl hover:text-red-700 flex items-center justify-center" data-id="${data.id}" style="padding-top:50%;">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    `;

                    document.getElementById('lista-fatores').appendChild(novoItem);


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
    if (!e.target.closest('.btn-excluir-fator')) return;

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
});
/* Fim da exclusão de fatores */
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

