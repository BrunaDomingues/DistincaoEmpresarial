<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                {{ __('Relatório de Classificação') }}
            </h2>
        </div>
    </x-slot>

    <x-relatorio-conteudo>
        <x-relatorio-filtros>
            <form method="POST" action="{{ route('relatorios.classificacao.filtrar') }}" class="relatorio-form-stack">
                @csrf
                <div>
                    <label for="formulario_id" class="font-medium text-gray-700 dark:text-gray-300">
                        Selecione o Questionário:
                    </label>
                    <select name="formulario_id" id="formulario_id" required>
                        <option value="">-- Selecione --</option>
                        @foreach($formularios as $form)
                            <option value="{{ $form->id }}">{{ $form->titulo }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="relatorio-toolbar-btn">
                    Gerar Relatório
                </button>
            </form>
        </x-relatorio-filtros>
    </x-relatorio-conteudo>
</x-app-layout>
