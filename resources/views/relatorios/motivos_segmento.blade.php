<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            Motivos de escolha por segmento
        </h2>
    </x-slot>

    @include('relatorios.partials.estilos-mobile')

    <div class="py-12"
         x-data="filtroMotivos(@js($passos), @js($segmentoId), @js($resultado['categoria'] ?? ''))">
        <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-950 dark:border-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-100">
                Escolha a categoria e o segmento para ver um gráfico de motivos de escolha para cada empresa.
                Ex.: Comércio → Loja de Brinquedos.
            </div>

            <form method="GET" action="{{ route('relatorios.motivos-segmento') }}" class="relatorio-form-mobile space-y-4">
                <div>
                    <label for="categoria" class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Categoria</label>
                    <select id="categoria" x-model="categoria"
                        class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-xl">
                        <option value="">— Todas —</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria }}">{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="segmento_id" class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Segmento</label>
                    <select name="segmento_id" id="segmento_id" x-model="segmentoId" required
                        class="border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white p-2 rounded w-full max-w-xl">
                        <option value="">— Selecione —</option>
                        <template x-for="passo in segmentosFiltrados" :key="passo.id">
                            <option :value="passo.id" x-text="passo.nome + ' — ' + (passo.formulario || '')"></option>
                        </template>
                    </select>
                </div>

                <button type="submit"
                    class="btn-relatorio bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                    Gerar gráficos
                </button>
            </form>

            @if ($resultado)
                @forelse ($resultado['graficos'] as $grafico)
                    <section class="rounded-lg border bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-center text-base font-semibold text-gray-900 dark:text-white">
                            {{ $resultado['categoria'] }} – {{ $resultado['nome_segmento'] }}
                        </h3>
                        <p class="mb-1 text-center text-sm text-gray-600 dark:text-gray-300">
                            {{ $resultado['nome_segmento'] }} - {{ $grafico['canonical'] }}
                        </p>
                        <p class="mb-4 text-center text-xs text-gray-500 dark:text-gray-400">
                            {{ $grafico['posicao'] }}º lugar · {{ $grafico['total'] }} menção(ões)
                        </p>

                        @if (count($grafico['motivos']) === 0)
                            <p class="text-center text-sm text-gray-500">Nenhum motivo de escolha registrado para esta empresa.</p>
                        @else
                            <div class="relative mx-auto max-w-3xl" style="height: {{ max(280, count($grafico['motivos']) * 52) }}px;">
                                <canvas class="grafico-motivos-empresa" data-motivos="{{ json_encode($grafico['motivos'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}"></canvas>
                            </div>
                            <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">Fonte: Dados primários</p>
                        @endif
                    </section>
                @empty
                    <p class="text-gray-600 dark:text-gray-300">Nenhuma empresa mencionada neste segmento.</p>
                @endforelse
            @endif
        </div>
    </div>

    @if ($resultado)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const verdes = ['#b7e4c7', '#74c69d', '#40916c', '#1b4332', '#081c15', '#95d5b2', '#52b788'];

                const montarGrafico = (canvas, motivos) => {
                    if (!canvas || !motivos.length || typeof ChartDataLabels === 'undefined') {
                        return;
                    }

                    new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: motivos.map((item) => item.label),
                            datasets: [{
                                data: motivos.map((item) => item.percentual),
                                backgroundColor: motivos.map((_, i) => verdes[i % verdes.length]),
                                borderSkipped: false,
                                barPercentage: 0.72,
                                categoryPercentage: 0.8,
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            const item = motivos[context.dataIndex];
                                            const percentual = Number(context.raw).toLocaleString('pt-BR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            });
                                            return `${percentual}% (${item?.total ?? 0} menções)`;
                                        },
                                    },
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'right',
                                    color: '#111827',
                                    formatter: (value) => Number(value).toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                    }) + '%',
                                },
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    suggestedMax: Math.max(40, Math.ceil(Math.max(...motivos.map((item) => item.percentual)) / 10) * 10),
                                    ticks: {
                                        callback: (value) => Number(value).toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        }) + '%',
                                    },
                                    grid: { color: '#e5e7eb' },
                                },
                                y: {
                                    grid: { display: false },
                                },
                            },
                            layout: { padding: { right: 56 } },
                        },
                        plugins: [ChartDataLabels],
                    });
                };

                document.querySelectorAll('.grafico-motivos-empresa').forEach((canvas) => {
                    const motivos = JSON.parse(canvas.dataset.motivos || '[]');
                    montarGrafico(canvas, motivos);
                });
            });
        </script>
    @endif

    <script>
        function filtroMotivos(passos, segmentoIdInicial, categoriaInicial) {
            return {
                passos,
                categoria: categoriaInicial || '',
                segmentoId: segmentoIdInicial ? String(segmentoIdInicial) : '',
                init() {
                    this.$watch('categoria', () => {
                        const ids = this.segmentosFiltrados.map((passo) => String(passo.id));
                        if (this.segmentoId && ! ids.includes(this.segmentoId)) {
                            this.segmentoId = '';
                        }
                    });
                },
                get segmentosFiltrados() {
                    if (!this.categoria) {
                        return this.passos;
                    }
                    return this.passos.filter((passo) => passo.categoria === this.categoria);
                },
            };
        }
    </script>
</x-app-layout>
