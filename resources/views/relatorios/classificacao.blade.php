<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('Relatório de Classificação') }}
        </h2>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('relatorios.classificacao.filtrar') }}" class="relatorio-form-mobile mb-6">
                @csrf
                <label for="formulario_id" class="block font-medium mb-1 text-gray-700 dark:text-gray-300">
                    Selecione o Questionário:
                </label>
                <select name="formulario_id" id="formulario_id"
                    class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-md">
                    <option value="">-- Selecione --</option>
                    @foreach($formularios as $form)
                        <option value="{{ $form->id }}">{{ $form->titulo }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="btn-relatorio mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                    Gerar Relatório
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
