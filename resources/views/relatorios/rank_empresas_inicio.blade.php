<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            Ranking de empresas (insight)
        </h2>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100">
                <p class="font-medium">Somente leitura no ranking</p>
                <p class="mt-1">
                    Esta tela agrupa nomes parecidos <strong>em memória</strong> para exibir o ranking — as respostas originais no banco não são alteradas.
                    Quando aparecer uma grafia errada conhecida, cadastre a correspondência em
                    <a href="{{ route('insight.empresa-aliases.index') }}" class="underline font-medium">Correspondências de empresas</a>.
                </p>
            </div>

            <form method="POST" action="{{ route('insight.ranking-empresas.analisar') }}" class="relatorio-form-mobile mb-6">
                @csrf
                <label for="formulario_id" class="block font-medium mb-1 text-gray-700 dark:text-gray-300">
                    Questionário
                </label>
                <select name="formulario_id" id="formulario_id"
                    class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-md"
                    required>
                    <option value="">— Selecione —</option>
                    @foreach ($formularios as $form)
                        <option value="{{ $form->id }}">{{ $form->titulo }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="btn-relatorio mt-3 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                    Analisar menções
                </button>
            </form>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('insight.empresa-aliases.index') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                    Gerenciar correspondências de nomes →
                </a>
            </p>
        </div>
    </div>
</x-app-layout>
