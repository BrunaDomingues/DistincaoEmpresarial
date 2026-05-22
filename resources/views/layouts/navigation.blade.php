<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-85 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 xl:-my-px xl:ms-10 xl:flex">
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
                            <x-nav-link :href="route('validar-envios.index')" :active="request()->routeIs('validar-envios.*')">
                                Validar envios
                            </x-nav-link>
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                Usuários
                            </x-nav-link>
                            {{-- Dropdown de Relatórios --}}
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            style="margin-top: 1.5rem;">
                                        Relatórios
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
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

            <!-- Settings Dropdown -->
            <div class="hidden xl:flex xl:items-center xl:ms-6">
                <div class="px-2">
                    <button
                        x-data="{ dark: document.documentElement.classList.contains('dark') }"
                        @click="
                            dark = !dark;
                            dark
                                ? document.documentElement.classList.add('dark')
                                : document.documentElement.classList.remove('dark')
                        "
                        class="text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white transition"
                    >
                        <i :class="dark ? 'bx bx-sun' : 'bx bx-moon'" class="text-xl"></i>
                    </button>
                </div>
                <div class="px-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                        <div class="block px-4 py-2 mb-0 text-[0.7875rem] text-gray-400 dark:text-gray-300 whitespace-nowrap">
                        <div class="font-small text-sm text-gray-500 dark:text-gray-400">Olá, {{ Auth::user()->name }}!</div>
                        </div>
                            {{--
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            --}}
                            <div class="h-px border-t border-gray-200 opacity-100 mx-[-1] my-2 overflow-hidden"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')" onclick="confirmarLogout(event)">
                                    {{ __('Sair') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center xl:hidden">
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden xl:hidden">
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
                    <x-responsive-nav-link :href="route('validar-envios.index')" :active="request()->routeIs('validar-envios.*')">
                        Validar envios
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

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 pb-2">
                <button
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="
                        dark = !dark;
                        dark
                            ? document.documentElement.classList.add('dark')
                            : document.documentElement.classList.remove('dark')
                    "
                    type="button"
                    class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition"
                >
                    <i :class="dark ? 'bx bx-sun' : 'bx bx-moon'" class="text-xl"></i>
                    <span x-text="dark ? 'Modo claro' : 'Modo escuro'"></span>
                </button>
            </div>

            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">Olá, {{ Auth::user()->name }}!</div>
               {{-- <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div> --}}
            </div>
            
            <div class="mt-3 space-y-1">
               {{--  <h6 class="text-overflow m-0">Olá, {{ Auth::user()->name }}!</h6> --}}
                {{--
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                --}}
                <!-- Authentication -->
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
