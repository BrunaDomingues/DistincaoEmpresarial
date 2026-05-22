<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Relatório de Respondentes por Bairro') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4">
        <form method="POST" action="{{ route('relatorios.classificacao.filtrar') }}" class="mb-6">
            @csrf
            <label for="formulario_id" class="block font-medium mb-1">Selecione o Questionário:</label>
            <select name="formulario_id" id="formulario_id" class="border p-2 rounded w-full max-w-md">
                <option value="">-- Selecione --</option>
                @foreach($formularios as $form)
                    <option value="{{ $form->id }}">{{ $form->titulo }}</option>
                @endforeach
            </select>
            <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded">Gerar Relatório</button>
        </form>
    </div>
</x-app-layout>
