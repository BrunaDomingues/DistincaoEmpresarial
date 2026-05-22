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
        }

        .chart-container, .recent-actions, .system-status {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        .chart-scroll {
            max-width: 100%;
            overflow-x: auto;
        }

        .chart-scroll canvas {
            max-width: 100%;
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
                            {{--
                            <button style="background: none; border: none; color: var(--primary); cursor: pointer;">
                                View All
                            </button>
                            --}}
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
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Bairros --}}
                    <div class="chart-container">
                        <div class="section-header">
                            <h3 class="section-title">Distribuição por Bairros</h3>
                        </div>
                        <div class="chart-scroll">
                            <canvas id="graficoBairros" height="300"></canvas>
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
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
function atualizarEnvios() {
    fetch('/envios-por-usuario')
        .then(response => response.json())
        .then(data => {
            const list = document.querySelector('.status-list');
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

            // Adiciona totalizador
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
        const ctx = document.getElementById('graficoBairros').getContext('2d');

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
                maintainAspectRatio: true,
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
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        };

        // Verifique se ChartDataLabels está disponível antes de criar o gráfico
        if (typeof ChartDataLabels !== 'undefined') {
            new Chart(ctx, config);
        } else {
            console.error('ChartDataLabels não foi carregado corretamente.');
        }
    });
</script>

<!-- Importa o plugin para mostrar os totais nas barras -->

</x-app-layout>