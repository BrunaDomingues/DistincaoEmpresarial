<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                    Respostas do envio
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $envio->formulario->titulo ?? '—' }}
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                ← Voltar ao dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Usuário</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $envio->usuario->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">E-mail</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $envio->usuario->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Data do envio</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $dataEnvio }}</dd>
                    </div>
                    @if ($envio->duracao_em_segundos)
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Duração</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $envio->duracao_formatada }}</dd>
                        </div>
                    @endif
                    @if ($envio->bairro || $envio->cidade)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 dark:text-gray-400">Localização</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                {{ collect([$envio->rua, $envio->bairro, $envio->cidade, $envio->estado])->filter()->implode(', ') ?: '—' }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </section>

            @forelse ($respostasPorPasso as $passoTitulo => $respostas)
                <section class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <header class="bg-gray-100 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $passoTitulo }}</h3>
                    </header>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($respostas as $resposta)
                            <div class="px-4 py-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $resposta->pergunta->pergunta ?? 'Pergunta removida' }}
                                </p>
                                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $resposta->resposta ?: '—' }}</p>
                                @if ($resposta->fator)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        Fator: {{ $resposta->fator->titulo ?? '' }}
                                        @if ($resposta->input_fator)
                                            — {{ $resposta->input_fator }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-8 text-center text-gray-500 dark:text-gray-400">
                    Nenhuma resposta registrada para este envio.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
