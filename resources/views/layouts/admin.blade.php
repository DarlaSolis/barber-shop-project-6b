<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BarberPro') - Panel de Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50">
    <div class="flex">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white min-h-screen p-6 border-r border-gray-200">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-black">BarberPro</h1>
                <p class="text-sm text-gray-600">Panel de Control</p>
            </div>

            <nav class="space-y-3">
                @if(Auth::check() && Auth::user()->isBarber())
                    {{-- Opciones para el Barbero --}}
                    <a href="{{ route('barber.index') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('barber.index') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Panel Barbero
                    </a>
                    <a href="{{ route('appointments.create') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('appointments.create') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Agendar Cita
                    </a>
                @elseif(Auth::check() && Auth::user()->role === 'user')
                    {{-- Opciones para el Cliente / Usuario Normal --}}
                    <a href="{{ route('appointments.create') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('appointments.create') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Agendar Cita
                    </a>
                @else
                    {{-- Opciones para Administrador / General --}}
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('barber.index') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('barber.index') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Panel Barbero
                    </a>
                    <a href="{{ route('appointments.create') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('appointments.create') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Agendar Cita
                    </a>
                    <a href="{{ route('registros.index') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('registros.index') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Registrar Cobros
                    </a>
                    <a href="{{ route('clientes.index') }}"
                        class="block px-4 py-3 rounded-lg {{ request()->routeIs('clientes.index') ? 'bg-gray-800 text-white' : 'text-black hover:bg-gray-100' }}">
                        Gestión de Clientes
                    </a>
                @endif
            </nav>

            <div class="mt-12 pt-6 border-t border-gray-200">
                <p class="text-sm text-black font-semibold">{{ Auth::user()?->name ?? 'Usuario' }}</p>
                <p class="text-xs text-gray-500 mb-3 capitalize">
                    {{ Auth::user()?->isBarber() ? 'Barbero' : (Auth::user()?->role === 'admin' ? 'Administrador' : 'Cliente') }}
                </p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline text-sm font-medium">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>
</body>

</html>