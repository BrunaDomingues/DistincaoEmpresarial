<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Análise do Envio #{{ $envio->id }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <div class="mt-4">
                <h4 class="text-md font-semibold">Localização aproximada do envio</h4>
                <div id="map" class="w-full h-64 mt-2 rounded shadow"></div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <strong>Rua:</strong> <span id="rua">Carregando...</span>
                </p>
            </div>

            <form method="POST" action="{{ route('validar-envios.store', $envio->id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    @foreach ($envio->formulario->passos as $passo)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold mb-2">Grupo: {{ $passo->titulo }}</h2>

                            @foreach ($passo->perguntas as $pergunta)
                                @php
                                    $resposta = $envio->respostas->firstWhere('pergunta_id', $pergunta->id);
                                    $respostaTratada = $resposta?->respostaTratada;
                                @endphp

                                <div class="border-b pb-4 mb-4">
                                    <h3 class="text-md font-medium">{{ $pergunta->pergunta }}</h3>

                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <strong>Resposta original:</strong> {{ $resposta?->resposta ?? '—' }}
                                    </p>

                                    <label class="text-sm text-gray-700 dark:text-gray-200">
                                        <strong>Resposta tratada:</strong>
                                    </label>
                                    <input
                                        type="text"
                                        name="respostas_tratadas[{{ $resposta->id ?? 'null' }}][resposta_tratada]"
                                        value="{{ $respostaTratada->resposta_tratada ?? '' }}"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        {{ is_null($resposta) ? 'disabled' : '' }}
                                    >
                                    {{-- Checkbox de validação --}}
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                name="respostas_tratadas[{{ $resposta->id ?? 'null' }}][validada]"
                                                value="1"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
                                                {{ isset($respostaTratada) && $respostaTratada->conferida ? 'checked' : '' }}
                                                {{ is_null($resposta) ? 'disabled' : '' }}
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">Resposta conferida</span>
                                        </label>
                                    </div>

                                    {{-- Exibir fator de satisfação da resposta --}}
                                    <div class="mt-4">
                                        <h4 class="text-sm font-semibold">Fator de Satisfação</h4>
                                        @if ($resposta->pergunta->usaFatoresSatisfacao())
                                            @if ($resposta->fator)
                                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                                    {{ $resposta->fator->titulo }}
                                                    @if ($resposta->fator->titulo === 'Outro')
                                                        - <span class="italic">{{ $resposta->input_fator }}</span>
                                                    @endif
                                                </p>
                                            @else
                                                <p class="text-sm text-red-600 dark:text-red-400">
                                                    Nenhum fator de satisfação selecionado
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                                Pergunta não utiliza fatores de satisfação
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-between">
                    {{-- Botão de Voltar --}}
                    <a href="{{ route('validar-envios.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Voltar
                    </a>

                    {{-- Botão de Salvar --}}
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Salvar Análise
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function initMap(lat, lon) {
        const map = L.map('map').setView([lat, lon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
        L.marker([lat, lon])
         .addTo(map)
         .bindPopup('Local aproximado do envio')
         .openPopup();

        // Buscar nome da rua
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(res => res.json())
            .then(data => {
                const rua = data.address.road || data.display_name || 'Rua não encontrada';
                document.getElementById('rua').textContent = rua;
            })
            .catch(() => {
                document.getElementById('rua').textContent = 'Erro ao buscar rua';
            });
    }

    document.addEventListener('DOMContentLoaded', async function () {
        let lat = @json($envio->latitude);
        let lon = @json($envio->longitude);

        if (lat && lon) {
            initMap(lat, lon);
        } else {
            try {
                const res = await fetch('https://ipapi.co/json/');
                const data = await res.json();
                lat = data.latitude;
                lon = data.longitude;
                if (lat && lon) {
                    initMap(lat, lon);
                } else {
                    document.getElementById('rua').textContent = 'Localização indisponível';
                }
            } catch (error) {
                document.getElementById('rua').textContent = 'Erro ao buscar localização via IP';
            }
        }
    });
</script>

</x-app-layout>
