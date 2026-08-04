<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reserva Online') - BarberPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-amber-500 selection:text-slate-950">

    <!-- HEADER / NAV PÚBLICO -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center font-extrabold text-slate-950 text-xl shadow-lg shadow-amber-500/20">
                    ✂️
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white">BarberPro</span>
                    <span class="text-xs block text-amber-500 font-semibold tracking-wider uppercase">Reserva Online</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-lg transition border border-slate-700">
                        Ir al Panel
                    </a>
                @else
                    <span class="text-xs text-slate-400 hidden sm:inline">¿Ya tienes cuenta?</span>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 text-sm font-bold rounded-lg transition shadow-md shadow-amber-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Iniciar Sesión
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL CENTRADO -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 py-8 sm:py-12">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-800 bg-slate-900/50 py-6 text-center text-xs text-slate-500">
        <p>© {{ date('Y') }} BarberPro — Todos los derechos reservados. Reserva tu corte sin filas.</p>
    </footer>

</body>
</html>
