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
<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-200">
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white dark:bg-slate-800 min-h-screen p-6 border-r border-gray-200 dark:border-slate-700 flex flex-col justify-between transition-colors duration-200 shadow-lg">
            <div>
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">BarberPro</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Sistema de Barbería</p>
                    </div>
                </div>

                <nav class="space-y-2">
                    @if(!Auth::check())
                        {{-- Opciones para Usuario No Autenticado (Público) --}}
                        <a href="{{ route('appointments.create') }}"
                            class="block px-4 py-3 rounded-lg bg-amber-600 text-white font-medium hover:bg-amber-500 transition">
                            ✂️ Agendar Cita
                        </a>
                    @elseif(Auth::user()->isBarber())
                        {{-- Barbero --}}
                        <a href="{{ route('barber.index') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('barber.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            💈 Panel Barbero
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            ✂️ Agendar Cita
                        </a>
                    @elseif(Auth::user()->isCliente())
                        {{-- Cliente --}}
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            📋 Mis Citas
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            ✂️ Agendar Cita
                        </a>
                    @else
                        {{-- Administrador / General --}}
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            📊 Dashboard
                        </a>
                        <a href="{{ route('barber.index') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('barber.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            💈 Panel Barbero
                        </a>
                        <a href="{{ route('appointments.create') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('appointments.create') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            ✂️ Agendar Cita
                        </a>
                        <a href="{{ route('registros.index') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('registros.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            💰 Registrar Cobros
                        </a>
                        <a href="{{ route('clientes.index') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('clientes.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                            👥 Gestión de Clientes
                        </a>
                        @if(Route::has('reportes.index'))
                            <a href="{{ route('reportes.index') }}" class="block px-4 py-3 rounded-lg font-medium transition {{ request()->routeIs('reportes.index') ? 'bg-amber-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                📈 Reportes
                            </a>
                        @endif
                    @endif
                </nav>
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-slate-700">
                <!-- Botón de Modo Oscuro / Claro -->
                <button onclick="toggleTheme()" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold w-full text-left bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-amber-400 border border-gray-300 dark:border-slate-600 hover:opacity-90 transition mb-4">
                    <span class="flex items-center gap-2">
                        <span id="theme-icon">🌙</span>
                        <span id="theme-text">Modo Oscuro</span>
                    </span>
                    <span class="text-[10px] opacity-75">Cambiar</span>
                </button>

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
                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">
                            🚪 Cerrar Sesión
                        </button>
                    </form>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Modo Público</p>
                    <a href="{{ route('login') }}" class="inline-block w-full text-center px-3 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-lg text-xs font-semibold transition">
                        🔑 Iniciar Sesión
                    </a>
                @endif
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 p-8 bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
            @yield('content')
        </main>
    </div>

    <script>
        function updateThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('theme-icon');
            const text = document.getElementById('theme-text');
            if (icon && text) {
                icon.textContent = isDark ? '🌙' : '☀️';
                text.textContent = isDark ? 'Modo Oscuro' : 'Modo Claro';
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