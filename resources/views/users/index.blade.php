<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Usuários') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
            <div class="text-end">
                <a href="{{ route('users.create') }}"
                   class="mb-4 inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition">
                    Novo Usuário
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 text-green-600 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Nome</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Email</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Admin</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-left">Status</th>
                            <th class="text-gray-700 dark:text-gray-200 px-4 py-2 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->email }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->is_admin ? 'Sim' : 'Não' }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-100">{{ $user->ativo ? 'Ativo' : 'Inativo' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <!-- Editar -->
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="p-2 rounded-full text-blue-500 transition-transform transform hover:scale-110">
                                            <i class="bx bx-edit text-xl"></i>
                                        </a>

                                        <!-- Excluir -->
                                        <button type="button"
                                                onclick="deleteUser(event, '{{ route('users.destroy', $user) }}')"
                                                class="p-2 rounded-full text-red-500 transition-transform transform hover:scale-110">
                                            <i class="bx bx-trash text-xl"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
<script>
    function deleteUser(event, url) {
        event.preventDefault();  // Evita que o formulário seja enviado diretamente
        
        // Exibe o SweetAlert de confirmação
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                // Caso o usuário confirme, envia o formulário de exclusão
                const form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(form);
                form.submit(); // Envia o formulário de exclusão
            }
        });
    }
</script>
</x-app-layout>
