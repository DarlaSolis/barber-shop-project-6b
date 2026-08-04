<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BarberPro') - Panel de Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-200">

    <!-- BARRA SUPERIOR: solo visible en mobile (hamburguesa + título) -->
    <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 sticky top-0 z-30 px-4 py-3 flex items-center justify-between shadow-sm md:hidden">
        <div class="flex items-center gap-3">
            <button id="hamburger-btn" onclick="toggleSidebar()" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none transition" aria-label="Abrir Menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight text-amber-600 dark:text-amber-400">BarberPro</a>
        </div>
    </header>

    <div class="flex relative min-h-[calc(100vh-57px)] md:min-h-screen">
        <!-- OVERLAY PARA MOBILE -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity"></div>

        <!-- SIDEBAR FIJO: no se mueve con el scroll de la página, ni en mobile ni en desktop -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 flex flex-col justify-between p-6 transition-transform duration-300 -translate-x-full md:translate-x-0 shadow-lg md:shadow-none overflow-y-auto">
            <div>
                <div class="mb-6 hidden md:block">
                    <h2 class="text-xl font-bold text-amber-600 dark:text-amber-400">BarberPro</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Panel de Control</p>
                </div>

                <nav class="space-y-1.5">
                    @if(!Auth::check())
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Agendar Cita
                        </a>
                    @elseif(Auth::user()->isBarber())
                        <a href="{{ route('barber.index') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('barber.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Panel Barbero
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Agendar Cita
                        </a>
                    @elseif(Auth::user()->isCliente())
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Mis Citas
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Agendar Cita
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('barber.index') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('barber.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Panel Barbero
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Agendar Cita
                        </a>
                        <a href="{{ route('registros.index') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('registros.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Registrar Cobros
                        </a>
                        <a href="{{ route('clientes.index') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('clientes.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            Gestión de Clientes
                        </a>
                        @if(Route::has('reportes.index'))
                            <a href="{{ route('reportes.index') }}" class="block px-4 py-2.5 rounded-lg font-medium text-sm transition {{ request()->routeIs('reportes.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                Reportes
                            </a>
                        @endif
                    @endif
                </nav>

                <!-- Botón Modo Oscuro / Claro (dentro del sidebar) -->
                <div class="pt-4 mt-4 border-t border-gray-200 dark:border-slate-700">
                    <button onclick="toggleTheme()" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-amber-400 border border-gray-300 dark:border-slate-600 hover:opacity-80 transition">
                        <svg id="theme-icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg id="theme-icon-moon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <span id="theme-text">Modo Oscuro</span>
                    </button>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-slate-700">
                @if(Auth::check())
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mb-3 font-medium">
                        @if(Auth::user()->isAdminGeneral())
                            Administrador General
                        @elseif(Auth::user()->isEncargado())
                            Encargado de Sucursal
                        @elseif(Auth::user()->isBarber())
                            Barbero
                        @else
                            Cliente
                        @endif
                    </p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs font-semibold">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Acceso Público</p>
                    <a href="{{ route('login') }}" class="inline-block w-full text-center px-3 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-lg text-xs font-semibold transition">
                        Iniciar Sesión
                    </a>
                @endif
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL RESPONSIVO: margen izquierdo en desktop para no quedar debajo del sidebar fijo -->
        <main class="flex-1 p-4 md:p-8 bg-gray-50 dark:bg-slate-900 transition-colors duration-200 min-w-0 md:ml-64">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function updateThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            const text = document.getElementById('theme-text');

            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                text.textContent = 'Modo Claro';
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                text.textContent = 'Modo Oscuro';
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeUI();
        }

        updateThemeUI();
    </script>
</body>
</html>
