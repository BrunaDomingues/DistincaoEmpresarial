<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            Resultados por setor e bairro
        </h2>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-950 dark:border-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-100">
                Selecione um setor e, se desejar, uma cidade para visualizar o ranking de empresas em cada bairro.
                O relatório considera somente respostas tratadas e envios válidos.
            </div>

            @if ($formularios->isEmpty())
                <p class="text-gray-600 dark:text-gray-300">
                    Nenhum setor com resultados classificáveis foi encontrado.
                </p>
            @else
                <form method="POST" action="{{ route('relatorios.segmentos-bairros.analisar') }}" class="relatorio-form-mobile">
                    @csrf
                    <label for="segmento_id" class="block font-medium mb-1 text-gray-700 dark:text-gray-300">
                        Setor/segmento
                    </label>
                    <select name="segmento_id" id="segmento_id"
                        class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-xl"
                        required>
                        <option value="">— Selecione —</option>
                        @foreach ($formularios as $formulario)
                            <optgroup label="{{ $formulario->titulo }}">
                                @foreach ($formulario->passos as $segmento)
                                    <option value="{{ $segmento->id }}" @selected(old('segmento_id') == $segmento->id)>
                                        {{ $segmento->titulo }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('segmento_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <label for="cidade" class="mt-4 block font-medium mb-1 text-gray-700 dark:text-gray-300">
                        Cidade
                    </label>
                    <select name="cidade" id="cidade"
                        class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-xl">
                        <option value="">Todas as cidades</option>
                        @foreach ($cidades as $cidade)
                            <option value="{{ $cidade }}" @selected(old('cidade') === $cidade)>
                                {{ $cidade }}
                            </option>
                        @endforeach
                    </select>
                    @error('cidade')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="btn-relatorio mt-3 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                        Gerar relatório
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
