<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('Visualizar Formulário') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Veja os detalhes do formulário.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="mb-4 text-gray-900 dark:text-white space-y-2">
                <p><strong>{{ __('Título:') }}</strong> {{ $formulario->titulo }}</p>
                <p><strong>{{ __('Descrição:') }}</strong> {{ $formulario->descricao }}</p>
                <p><strong>{{ __('Data Início:') }}</strong> {{ $formulario->data_inicio }}</p>
                <p><strong>{{ __('Data Fim:') }}</strong> {{ $formulario->data_fim }}</p>
                <p><strong>{{ __('Criado por:') }}</strong> {{ $formulario->criador->name ?? '-' }}</p>
                <p><strong>{{ __('Última edição:') }}</strong> {{ $formulario->editor->name ?? '-' }}</p>
            </div>

            <div class="flex items-center">
                <a href="{{ route('formularios.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500 transition">
                    {{ __('Voltar') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
