<style>
    .nav-links-desktop {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-left: 2rem;
        flex-wrap: wrap;
    }
    .nav-relatorios-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0;
        border-bottom: 2px solid transparent;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.25rem;
        color: #6b7280;
        background: transparent;
        white-space: nowrap;
    }
    .nav-relatorios-btn:hover {
        color: #374151;
        border-bottom-color: #d1d5db;
    }
    .dark .nav-relatorios-btn {
        color: #9ca3af;
    }
    .dark .nav-relatorios-btn:hover {
        color: #d1d5db;
        border-bottom-color: #4b5563;
    }
    .nav-user-desktop {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        margin-left: 1rem;
    }
    .nav-hamburger {
        display: none;
        align-items: center;
    }
    @media (min-width: 1041px) {
        .nav-links-desktop { display: flex !important; }
        .nav-user-desktop { display: flex !important; }
        .nav-hamburger { display: none !important; }
        .nav-menu-mobile { display: none !important; }
    }
    @media (max-width: 1040px) {
        .nav-links-desktop { display: none !important; }
        .nav-user-desktop { display: none !important; }
        .nav-hamburger { display: flex !important; }
    }
</style>
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex min-w-0 flex-1 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="nav-links-desktop hidden min-[1041px]:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @auth
                        @if (Auth::user()->is_admin)
                            <x-nav-link :href="route('formularios.index')" :active="request()->routeIs('formularios.*')">
                                Formulários
                            </x-nav-link>   
                            <x-nav-link :href="route('respostas-tratadas.index')" :active="request()->routeIs('respostas-tratadas.*')">
                                Respostas Tratadas
                            </x-nav-link>
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                Usuários
                            </x-nav-link>
                            {{-- Dropdown de Relatórios --}}
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button type="button" class="nav-relatorios-btn focus:outline-none transition duration-150">
                                        Relatórios
                                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('relatorios.aplicadores') }}">
                                        Aplicadores Por Dia
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('relatorios.aplicadores.acumulado') }}">
                                        Aplicadores Acumulado
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('relatorios.classificacao') }}">
                                        Classificação
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('relatorios.bairros') }}">
                                        Respondentes por bairro
                                    </x-dropdown-link>
                                    {{-- Outros relatórios podem ser adicionados aqui --}}
                                </x-slot>
                            </x-dropdown>
                        @endif
                    @endauth
                    <x-nav-link :href="route('responder-formularios.index')" :active="request()->routeIs('responder-formularios.*')">
                        Responder Formulários
                    </x-nav-link>
                </div>
            </div>

            <!-- Menu do usuário -->
            <div class="nav-user-desktop hidden min-[1041px]:flex">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-1 rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-5 text-gray-500 transition hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-300">
                            <span class="max-w-[10rem] truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 fill-current shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                            Olá, {{ Auth::user()->name }}!
                        </div>

                        <button
                            type="button"
                            x-data="{ dark: document.documentElement.classList.contains('dark') }"
                            @click="
                                dark = !dark;
                                dark
                                    ? document.documentElement.classList.add('dark')
                                    : document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', dark ? 'dark' : 'light');
                            "
                            class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <i :class="dark ? 'bx bx-sun' : 'bx bx-moon'" class="text-lg"></i>
                            <span x-text="dark ? 'Modo claro' : 'Modo escuro'"></span>
                        </button>

                        <div class="mx-[-1px] my-2 border-t border-gray-200 dark:border-gray-600"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="confirmarLogout(event)">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="nav-hamburger -me-2 flex items-center min-[1041px]:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="nav-menu-mobile hidden min-[1041px]:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @auth
                @if (Auth::user()->is_admin)
                    <x-responsive-nav-link :href="route('formularios.index')" :active="request()->routeIs('formularios.*')">
                        Formulários
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('respostas-tratadas.index')" :active="request()->routeIs('respostas-tratadas.*')">
                        Respostas Tratadas
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        Usuários
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('relatorios.aplicadores')" :active="request()->routeIs('relatorios.aplicadores')">
                        Relatório: Aplicadores Por Dia
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('relatorios.aplicadores.acumulado')" :active="request()->routeIs('relatorios.aplicadores.acumulado')">
                        Relatório: Aplicadores Acumulado
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('relatorios.classificacao')" :active="request()->routeIs('relatorios.classificacao*')">
                        Relatório: Classificação
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('relatorios.bairros')" :active="request()->routeIs('relatorios.bairros')">
                        Relatório: Respondentes por bairro
                    </x-responsive-nav-link>
                @endif
            @endauth
            <x-responsive-nav-link :href="route('responder-formularios.index')" :active="request()->routeIs('responder-formularios.*')">
                Responder Formulários
            </x-responsive-nav-link>
        </div>

        <!-- Menu do usuário (mobile) -->
        <div class="border-t border-gray-200 pt-4 pb-3 dark:border-gray-600">
            <div class="px-4 font-medium text-base text-gray-800 dark:text-gray-200">
                Olá, {{ Auth::user()->name }}!
            </div>

            <div class="mt-2 space-y-1">
                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="
                        dark = !dark;
                        dark
                            ? document.documentElement.classList.add('dark')
                            : document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', dark ? 'dark' : 'light');
                    "
                    class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                >
                    <i :class="dark ? 'bx bx-sun' : 'bx bx-moon'" class="text-xl"></i>
                    <span x-text="dark ? 'Modo claro' : 'Modo escuro'"></span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="confirmarLogout(event)">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
