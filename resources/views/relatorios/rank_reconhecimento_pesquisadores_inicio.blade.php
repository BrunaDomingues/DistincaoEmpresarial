<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            Ranking por pesquisador — Não conheço / Conheço mas não lembro
        </h2>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                <p class="font-medium">O que este relatório mostra</p>
                <p class="mt-1">
                    Conta, por pesquisador, quantas vezes foram marcadas as opções sem nome de empresa
                    (<strong>Não conheço</strong> e <strong>Conheço mas não lembro</strong>) em cada questionário.
                    Também lista o total de envios válidos do pesquisador no formulário.
                </p>
            </div>

            <form method="POST" action="{{ route('insight.ranking-reconhecimento-pesquisadores.analisar') }}" class="relatorio-form-mobile mb-6">
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
                    Gerar ranking
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
