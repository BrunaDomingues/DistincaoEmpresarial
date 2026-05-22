<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ isset($user) ? 'Editar Usuário' : 'Novo Usuário' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-900 shadow rounded p-6">
                <form 
                    action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" 
                    method="POST" 
                    class="space-y-6"
                >
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nome</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $user->name ?? '') }}" 
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', $user->email ?? '') }}" 
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Senha {{ isset($user) ? '(deixe em branco para manter)' : '' }}
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirmar Senha</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div class="flex items-center space-x-2">
                        <input 
                            type="checkbox" 
                            name="is_admin" 
                            id="is_admin" 
                            class="rounded text-blue-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500"
                            {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
                        >
                        <label for="is_admin" class="text-sm text-gray-700 dark:text-gray-200">Administrador</label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input 
                            type="checkbox" 
                            name="ativo" 
                            id="ativo" 
                            class="rounded text-blue-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-blue-500"
                            {{ old('ativo', $user->ativo ?? true) ? 'checked' : '' }}
                        >
                        <label for="ativo" class="text-sm text-gray-700 dark:text-gray-200">Usuário Ativo</label>
                    </div>
                    
                    <div class="flex justify-between items-center pt-4">
                        <button 
                            type="submit" 
                            class="inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition"
                        >
                            {{ isset($user) ? 'Atualizar' : 'Cadastrar' }}
                        </button>

                        <a href="{{ route('users.index') }}" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white py-2 px-4 rounded transition">
                            Voltar
                        </a>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
