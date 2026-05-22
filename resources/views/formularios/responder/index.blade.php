<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Responder Formulários') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('error'))
                    <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                <ul>
                    @forelse ($formularios as $formulario)
                        <li class="mb-4 flex justify-between items-center border-b border-gray-300 dark:border-gray-600 pb-2">
                            <span class="text-gray-800 dark:text-gray-100">{{ $formulario->titulo }}</span>
                            <a href="{{ route('respostas.create', $formulario->id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                                Responder
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-300">Nenhum formulário disponível no momento.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
