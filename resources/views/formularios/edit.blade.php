<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('Editar Formulário') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Atualize as informações do formulário.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('formularios.update', $formulario) }}"
                  class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="titulo" :value="__('Título')" class="dark:text-gray-300" />
                    <x-text-input id="titulo" name="titulo" type="text"
                                  class="mt-1 block w-full dark:bg-gray-700 dark:text-white"
                                  value="{{ old('titulo', $formulario->titulo) }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                </div>

                <div>
                    <x-input-label for="descricao" :value="__('Descrição')" class="dark:text-gray-300" />
                    <x-text-input id="descricao" name="descricao" type="text"
                                  class="mt-1 block w-full dark:bg-gray-700 dark:text-white"
                                  value="{{ old('descricao', $formulario->descricao) }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('descricao')" />
                </div>

                <div>
                    <x-input-label for="data_inicio" :value="__('Data Início')" class="dark:text-gray-300" />
                    <x-text-input id="data_inicio" name="data_inicio" type="date"
                                  class="mt-1 block w-full dark:bg-gray-700 dark:text-white"
                                  value="{{ old('data_inicio', $formulario->data_inicio) }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('data_inicio')" />
                </div>

                <div>
                    <x-input-label for="data_fim" :value="__('Data Fim')" class="dark:text-gray-300" />
                    <x-text-input id="data_fim" name="data_fim" type="date"
                                  class="mt-1 block w-full dark:bg-gray-700 dark:text-white"
                                  value="{{ old('data_fim', $formulario->data_fim) }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('data_fim')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button class="dark:bg-blue-600 dark:hover:bg-blue-700">
                        {{ __('Atualizar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
