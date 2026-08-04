@extends('layouts.admin')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="max-w-6xl">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Gestión de Clientes</h1>
            <span class="text-gray-500 dark:text-gray-400 text-sm">Administra la información de tus clientes</span>
        </div>
        <button onclick="document.getElementById('modal-nuevo').classList.remove('hidden')"
            class="bg-gray-900 hover:bg-gray-700 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-semibold px-5 py-2 rounded-lg transition">
            + Nuevo Cliente
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-6">

        {{-- Panel izquierdo --}}
        <div class="w-80 flex-shrink-0">
            <input type="text" id="buscar-cliente" placeholder="Buscar cliente..."
                class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2.5 text-sm text-gray-700 dark:text-white bg-gray-50 dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500 mb-3"/>

            <div id="lista-clientes" class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 divide-y divide-gray-100 dark:divide-slate-700 transition-colors">
                @forelse($clientes as $cliente)
                    @php
                        $partes    = explode(' ', trim($cliente->name));
                        $iniciales = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
                        $activo    = request('client_id') == $cliente->id;
                    @endphp
                    <a href="{{ route('clientes.index', ['client_id' => $cliente->id]) }}"
                       class="cliente-item flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-700 transition {{ $activo ? 'bg-amber-50 dark:bg-slate-700' : '' }}"
                       data-nombre="{{ strtolower($cliente->name) }}">
                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ $iniciales }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $cliente->name }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $cliente->visit_count }} visitas</div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-6 text-center text-gray-400 dark:text-gray-500 text-sm">No hay clientes registrados</div>
                @endforelse
            </div>
        </div>

        {{-- Panel derecho --}}
        <div class="flex-1 min-h-96">
            @if($selectedCliente && $stats)
                @php
                    $partes    = explode(' ', trim($selectedCliente->name));
                    $iniciales = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
                @endphp

                {{-- Card de contacto --}}
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-4 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Información de Contacto</h3>
                        <div class="flex gap-2">
                            <button onclick="document.getElementById('modal-editar').classList.remove('hidden')"
                                class="border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 text-sm px-4 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                Editar
                            </button>
                            <button onclick="confirmarEliminar({{ $selectedCliente->id }}, '{{ addslashes($selectedCliente->name) }}')"
                                class="border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 text-sm px-4 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg p-2 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Nombre</div>
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $selectedCliente->name }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg p-2 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Teléfono</div>
                                <div class="text-sm text-gray-800 dark:text-gray-100">{{ $selectedCliente->phone ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg p-2 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Email</div>
                                <div class="text-sm text-gray-800 dark:text-gray-100 break-all">{{ $selectedCliente->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card principal --}}
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-6 mb-4 transition-colors">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                            {{ $iniciales }}
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $selectedCliente->name }}</div>
                            <div class="text-sm text-gray-400 dark:text-gray-500">Cliente desde {{ $selectedCliente->created_at->translatedFormat('F \d\e Y') }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg p-2 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Barbero Preferido</div>
                                <div class="text-sm text-gray-700 dark:text-gray-200">{{ $stats['barberoNombre'] }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="border border-gray-200 dark:border-slate-600 rounded-lg p-2 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Última Visita</div>
                                <div class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ $stats['ultimaVisita'] ? $stats['ultimaVisita']->format('d/m/Y') : '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 border-t border-gray-100 dark:border-slate-700 pt-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-500 dark:text-amber-400">{{ $stats['totalVisitas'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total Visitas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-500 dark:text-amber-400">${{ number_format($stats['totalGastado'], 2) }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total Gastado</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-500 dark:text-amber-400">${{ number_format($stats['promedio'], 2) }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Promedio/Visita</div>
                        </div>
                    </div>
                </div>

                {{-- Historial --}}
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-6 transition-colors">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Historial de Visitas</h3>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($historial as $cita)
                            <div class="flex justify-between items-start py-3">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $cita->service->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $cita->appointment_date->format('d/m/Y') }} • {{ $cita->barber->name ?? '—' }}
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-amber-500 dark:text-amber-400">
                                    ${{ number_format($cita->service->price ?? 0, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500 py-4">Sin historial</p>
                        @endforelse
                    </div>
                </div>

            @else
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 h-80 flex flex-col items-center justify-center gap-3 transition-colors">
                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="text-base font-semibold text-gray-700 dark:text-gray-200">Selecciona un Cliente</div>
                    <div class="text-sm text-gray-400 dark:text-gray-500">Busca y selecciona un cliente para ver su información completa</div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Nuevo Cliente --}}
<div id="modal-nuevo" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Nuevo Cliente</h2>
            <button onclick="document.getElementById('modal-nuevo').classList.add('hidden')" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">&times;</button>
        </div>
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Nombre completo</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"
                        placeholder="Ej. Juan Pérez"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"
                        placeholder="correo@ejemplo.com"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Teléfono <span class="text-gray-400 dark:text-gray-500">(opcional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"
                        placeholder="+52 555 000 0000"/>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-nuevo').classList.add('hidden')"
                    class="flex-1 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 py-2.5 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-gray-900 hover:bg-gray-700 dark:bg-amber-600 dark:hover:bg-amber-500 text-white py-2.5 rounded-lg text-sm font-semibold transition">
                    Registrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar Cliente --}}
@if($selectedCliente)
<div id="modal-editar" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Editar Cliente</h2>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">&times;</button>
        </div>
        <form method="POST" action="{{ route('clientes.update', $selectedCliente->id) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Nombre completo</label>
                    <input type="text" name="name" required value="{{ $selectedCliente->name }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" required value="{{ $selectedCliente->email }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Teléfono <span class="text-gray-400 dark:text-gray-500">(opcional)</span></label>
                    <input type="text" name="phone" value="{{ $selectedCliente->phone }}"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"
                        placeholder="+52 555 000 0000"/>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-editar').classList.add('hidden')"
                    class="flex-1 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 py-2.5 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 bg-gray-900 hover:bg-gray-700 dark:bg-amber-600 dark:hover:bg-amber-500 text-white py-2.5 rounded-lg text-sm font-semibold transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($selectedCliente)
<form id="form-eliminar" method="POST" action="{{ route('clientes.destroy', $selectedCliente->id) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminar(id, nombre) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Vas a eliminar a <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: isDark ? '#1e293b' : '#fff',
            color: isDark ? '#f1f5f9' : '#1f2937',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-eliminar').submit();
            }
        });
    }

    document.getElementById('buscar-cliente').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.cliente-item').forEach(function (el) {
            el.style.display = el.dataset.nombre.includes(q) ? '' : 'none';
        });
    });

    // Cerrar modales al click fuera
    ['modal-nuevo', 'modal-editar'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('click', function(e) {
            if (e.target === el) el.classList.add('hidden');
        });
    });
</script>
@endsection
