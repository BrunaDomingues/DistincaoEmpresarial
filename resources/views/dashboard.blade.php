<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --dark: #212529;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #adb5bd;
        }

        .dashboard,
        .dashboard * {
            box-sizing: border-box;
        }

        .dashboard {
            display: flex;
            width: 100%;
            max-width: 100%;
            min-height: calc(100vh - 5rem);
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #eee;
        }

        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .menu-item:hover {
            background-color: #f0f4ff;
            color: var(--primary);
        }

        .menu-item.active {
            background-color: #e6ebff;
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .search-bar {
            position: relative;
            width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 30px;
            outline: none;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            border-color: var(--primary);
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .user-actions {
            display: flex;
            align-items: center;
        }

        .notification {
            position: relative;
            margin-right: 20px;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stat-icon.users {
            background-color: var(--primary);
        }

        .stat-icon.sessions {
            background-color: var(--success);
        }

        .stat-icon.health {
            background-color: var(--warning);
        }

        .stat-icon.alerts {
            background-color: var(--danger);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Main Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            align-items: stretch;
        }

        .left-column,
        .right-column {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .chart-container,
        .recent-actions,
        .system-status {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .recent-actions,
        .system-status {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .chart-container {
            margin-bottom: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .section-link {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
        }

        .section-link:hover {
            text-decoration: underline;
        }

        .chart-placeholder {
            height: 300px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
        }

        .action-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .action-item:last-child {
            border-bottom: none;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e6ebff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .action-details {
            flex: 1;
        }

        .action-title {
            font-weight: 500;
            margin-bottom: 3px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .action-time {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            font-weight: 500;
        }

        .status-value {
            font-weight: 600;
        }

        .status-value.good {
            color: var(--success);
        }

        .status-value.warning {
            color: var(--warning);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .action-btn {
            padding: 15px;
            background-color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: #f0f4ff;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .chart-bairros-section {
            grid-column: 1 / -1;
            width: 100%;
            max-width: 100%;
        }

        .chart-scroll {
            max-width: 100%;
            overflow-x: auto;
            width: 100%;
        }

        .chart-scroll--bairros {
            position: relative;
            height: 420px;
            width: 100%;
            overflow-x: auto;
        }

        .chart-scroll--bairros canvas {
            display: block;
            height: 100% !important;
        }

        .chart-scroll--geo canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        .chart-scroll--geo {
            position: relative;
            min-height: 320px;
            width: 100%;
        }

        .mapa-envios-canvas {
            height: 720px;
            width: 100%;
            border-radius: 8px;
            margin-top: 4px;
        }

        .mapa-envios-container .leaflet-container {
            font-family: inherit;
        }

        .mapa-envios-meta {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .mapa-popup {
            line-height: 1.45;
            font-size: 0.9rem;
        }

        .mapa-popup strong {
            display: block;
            margin-bottom: 4px;
        }

        /* Responsive Design */
        @media (max-width: 1100px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar-header h2, .menu-item span {
                display: none;
            }
            
            .menu-item {
                justify-content: center;
                padding: 15px 0;
            }
            
            .menu-item i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr 1fr;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .search-bar {
                width: 100%;
                margin-bottom: 15px;
            }
            
            .user-actions {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
        </style>
        {{--
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    --}}
    @if (Auth::user()->is_admin)
    <div class="dashboard">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">{{$totalEnvios}}</div>
                            <div class="stat-label">Total de envios</div>
                        </div>
                        <div class="stat-icon users">
                            <i class="fas fa-list-check"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">{{$totalQuestionarios}}</div>
                            <div class="stat-label">Total de questionários</div>
                        </div>
                        <div class="stat-icon sessions">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">{{$totalBairros}}</div>
                            <div class="stat-label">Total de bairros</div>
                        </div>
                        <div class="stat-icon health">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">{{$totalUsuarios}}</div>
                            <div class="stat-label">Total de usuários</div>
                        </div>
                        <div class="stat-icon alerts">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <div class="left-column">
                    <!-- Recent Actions -->
                    <div class="recent-actions">
                        <div class="section-header">
                            <h3 class="section-title">Envios recentes</h3>
                            <a href="{{ route('relatorios.envios-usuarios') }}" class="section-link">
                                Ver todos
                            </a>
                        </div>
                        <div class="actions-list">
                            @foreach ($enviosRecentes as $envio)
                                <div class="action-item">
                                    <div class="action-icon">
                                        <i class="fas fa-list-check"></i>
                                    </div>
                                    <div class="action-details">
                                        <div class="action-title">
                                            <span class="font-bold">{{ $envio->usuario }}</span> 
                                            concluiu uma aplicação no formulário {{$envio->titulo}}
                                        </div>
                                        <div class="action-time">{{ $envio->data_hora }}</div>
                                        <a href="{{ route('envios.show', $envio->id) }}" class="action-link" title="Ver respostas deste envio">
                                            <i class="fas fa-eye"></i> Ver respostas
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="right-column">
                    <!-- System Status -->
                    <div class="system-status">
                        <div class="section-header">
                            <h3 class="section-title">Total de envios por usuário</h3>
                            <i class="fas fa-sync-alt" style="color: var(--gray); cursor: pointer;"
                                onclick="atualizarEnvios()" title="Atualizar lista"></i>
                        </div>
                        <div class="status-list">
                            @foreach ($envios as $envio)
                                <div class="status-item">
                                    <span class="status-label">{{ $envio->name }}</span>
                                    <span class="status-value warning">{{ $envio->total_envios }} envios</span>
                                </div>
                            @endforeach

                            <div class="status-item">
                                <span class="font-bold">Total geral</span>
                                <span class="status-value font-bold text-green-600">
                                    {{ $envios->sum('total_envios') }} envios
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                     {{--
                    <div class="quick-actions">
                        <button class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add User</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-file-export"></i>
                            <span>Export Data</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-question-circle"></i>
                            <span>Help</span>
                        </button>
                    </div>
                    --}}
                </div>

                <div class="chart-container chart-bairros-section">
                    <div class="section-header">
                        <h3 class="section-title">Distribuição por Bairros</h3>
                    </div>
                    <div class="chart-scroll chart-scroll--bairros">
                        <canvas id="graficoBairros"></canvas>
                    </div>
                </div>

            </div>

            <div class="chart-container chart-bairros-section">
                <div class="section-header">
                    <h3 class="section-title">Geolocalização dos pesquisadores</h3>
                    <span class="mapa-envios-meta">Envios válidos por bairro e cidade informados</span>
                </div>
                @if (count($graficoGeoLabels) === 0)
                    <p class="mapa-envios-meta">Nenhum envio com localização registrado ainda.</p>
                @else
                    <div class="chart-scroll chart-scroll--geo" id="grafico-geo-wrap">
                        <canvas id="graficoGeoPesquisadores"></canvas>
                    </div>
                @endif
            </div>

            <div class="chart-container mapa-envios-container">
                <div class="section-header">
                    <h3 class="section-title">Mapa por bairro e cidade</h3>
                    @if (count($pontosMapaJson) > 0)
                        <span class="mapa-envios-meta">Uma bola por bairro</span>
                    @endif
                </div>
                @if (count($pontosMapaJson) === 0)
                    <p class="mapa-envios-meta" style="padding: 1rem 0;">Nenhum envio com bairro ou cidade informados ainda.</p>
                @else
                    <div id="mapa-envios" class="mapa-envios-canvas" role="region" aria-label="Mapa de envios por bairro"></div>
                @endif
            </div>

        </div>
    </div>


    @else
        <div class="py-12">
            <div class="max-w-85 mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("Login realizado com sucesso!") }}
                    </div>
                </div>
            </div>
        </div>
    @endif
    
@if (Auth::user()->is_admin)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
function escapeHtmlMapa(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const d = document.createElement('div');
    d.textContent = String(text);
    return d.innerHTML;
}

function atualizarEnvios() {
    const list = document.querySelector('.status-list');
    if (!list) {
        return;
    }
    fetch('/envios-por-usuario')
        .then(response => response.json())
        .then(data => {
            list.innerHTML = '';

            let totalGeral = 0;

            data.forEach(envio => {
                const item = document.createElement('div');
                item.className = 'status-item';
                item.innerHTML = `
                    <span class="status-label">${envio.name}</span>
                    <span class="status-value warning">${envio.total_envios} envios</span>
                `;
                list.appendChild(item);
                totalGeral += parseInt(envio.total_envios);
            });

            const totalItem = document.createElement('div');
            totalItem.className = 'status-item';
            totalItem.innerHTML = `
                <span class="status-label font-bold">Total geral</span>
                <span class="status-value font-bold text-green-600">${totalGeral} envios</span>
            `;
            list.appendChild(totalItem);
        })
        .catch(error => {
            console.error('Erro ao buscar dados:', error);
            alert('Erro ao atualizar os dados.');
        });
}

document.addEventListener('DOMContentLoaded', () => {
        const graficoEl = document.getElementById('graficoBairros');
        if (graficoEl && typeof Chart !== 'undefined') {
            const ctx = graficoEl.getContext('2d');

            const data = {
                @if (!empty($dadosPorBairro))
                    labels: {!! json_encode(collect($dadosPorBairro)->pluck('bairro')) !!},
                @else
                    labels: [],
                @endif

                datasets: [{
                    label: 'Total de Envios',
                    @if (!empty($dadosPorBairro))
                        data: {!! json_encode(collect($dadosPorBairro)->pluck('total')) !!},
                    @else
                        data: [],
                    @endif

                    backgroundColor: '#4361ee'
                }]
            };

            const config = {
                type: 'bar',
                data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: context => `Total: ${context.raw}`
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: (value) => value,
                            color: '#000'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Total' }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            };

            const labelsBairros = data.labels || [];
            if (labelsBairros.length > 0) {
                graficoEl.style.minWidth = Math.max(graficoEl.parentElement.clientWidth, labelsBairros.length * 42) + 'px';
            }

            if (typeof ChartDataLabels !== 'undefined') {
                new Chart(ctx, config);
            } else {
                console.error('ChartDataLabels não foi carregado corretamente.');
            }
        }

        const geoEl = document.getElementById('graficoGeoPesquisadores');
        const geoLabels = @json($graficoGeoLabels);
        const geoDatasets = @json($graficoGeoDatasets);
        if (geoEl && typeof Chart !== 'undefined' && Array.isArray(geoLabels) && geoLabels.length > 0) {
            const wrap = document.getElementById('grafico-geo-wrap');
            if (wrap) {
                const extraLegenda = Math.ceil((geoDatasets.length || 0) / 3) * 22;
                wrap.style.height = Math.max(360, geoLabels.length * 32 + extraLegenda) + 'px';
            }

            new Chart(geoEl.getContext('2d'), {
                type: 'bar',
                data: { labels: geoLabels, datasets: geoDatasets },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 },
                                padding: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${context.raw}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            beginAtZero: true,
                            title: { display: true, text: 'Envios' }
                        },
                        y: {
                            stacked: true,
                            title: { display: true, text: 'Pesquisador' }
                        }
                    }
                }
            });
        }

        const mapaEl = document.getElementById('mapa-envios');
        const pontosMapa = @json($pontosMapaJson);
        if (mapaEl && typeof L !== 'undefined' && Array.isArray(pontosMapa) && pontosMapa.length > 0) {
            const mapa = L.map('mapa-envios', { scrollWheelZoom: false });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(mapa);

            const bounds = [];
            pontosMapa.forEach((p) => {
                bounds.push([p.lat, p.lng]);
                const popupHtml = '<div class="mapa-popup"><strong>' + escapeHtmlMapa(p.endereco || '—') + '</strong>'
                    + escapeHtmlMapa((p.total || 0) + ' envio(s) respondidos nesta região') + '</div>';
                L.circleMarker([p.lat, p.lng], {
                    radius: 14,
                    color: '#4361ee',
                    weight: 2,
                    fillColor: '#4361ee',
                    fillOpacity: 0.92,
                }).addTo(mapa).bindPopup(popupHtml);
            });

            if (bounds.length === 1) {
                mapa.setView(bounds[0], 15);
            } else {
                mapa.fitBounds(L.latLngBounds(bounds), { padding: [40, 40], maxZoom: 15 });
            }
        }
    });
</script>
@endif

<!-- Importa o plugin para mostrar os totais nas barras -->

</x-app-layout>