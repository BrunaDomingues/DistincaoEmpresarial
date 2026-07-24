<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FormsQuest') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>
        <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900 overflow-x-hidden">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-85 mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Desenvolvido por
                    <a href="https://brunadomingues.com.br/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="font-medium text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 underline-offset-2 hover:underline">
                        Bruna Domingues Leite
                    </a>
                </div>
            </footer>
        </div>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        function confirmarLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Tem certeza que deseja sair?',
                text: "Você pode perder dados não salvos!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        }
        /*  document.addEventListener('DOMContentLoaded', () => {
                const toggleBtn = document.getElementById('toggle-theme');
                const themeIcon = document.getElementById('theme-icon');
                const html = document.documentElement;

                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    html.classList.add('dark');
                    themeIcon.classList.replace('bx-sun', 'bx-moon');
                } else {
                    html.classList.remove('dark');
                    themeIcon.classList.replace('bx-moon', 'bx-sun');
                }

                toggleBtn.addEventListener('click', () => {
                    html.classList.toggle('dark');
                    const isDark = html.classList.contains('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    themeIcon.classList.toggle('bx-sun', !isDark);
                    themeIcon.classList.toggle('bx-moon', isDark);
                });
            }); */
            setInterval(async () => {
                try {
                    const response = await fetch('/csrf-token');
                    const data = await response.json();

                    // Atualiza o token no <meta>
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta && data.token) {
                        meta.setAttribute('content', data.token);
                        console.log('CSRF token atualizado');
                    }
                } catch (error) {
                    console.warn('Erro ao atualizar o token CSRF', error);
                }
            }, 5 * 60 * 1000); // Atualiza a cada 5 minutos

            async function verificarSessao() {
                try {
                    const res = await fetch('/check-auth');
                    const data = await res.json();

                    if (!data.authenticated) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Sessão expirada',
                            text: 'Sua sessão expirou. Você será redirecionado para a página de login.',
                            timer: 5000,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        window.location.href = '/login';
                    }
                } catch (error) {
                    console.error('Erro ao verificar autenticação:', error);
                }
            }

            setInterval(verificarSessao, 30 * 60 * 1000); // Verifica a cada 30 minutos
            // Também pode rodar imediatamente ao carregar a página
            verificarSessao();
        </script>
    </body>
</html>
