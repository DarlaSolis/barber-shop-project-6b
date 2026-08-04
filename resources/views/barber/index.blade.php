@extends('layouts.admin')

@section('title', 'Panel del Barbero')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Panel del Barbero</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Gestiona tus citas de la jornada, haz check-in y consulta tus comisiones</p>
        </div>

        {{-- Filtros: (barbero solo para admin), rango de fechas, estado, exportar --}}
        <form method="GET" action="{{ route('barber.index') }}" class="flex flex-wrap items-end gap-3">
            @if(auth()->user()->role === 'admin')
                <div>
                    <label for="barber_id" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Barbero</label>
                    <select name="barber_id" id="barber_id" onchange="this.form.submit()"
                        class="px-3 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
                        @foreach($barbers as $b)
                            <option value="{{ $b->user_id }}" {{ $selectedBarberId == $b->user_id ? 'selected' : '' }}>
                                {{ $b->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label for="desde" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Desde</label>
                <input type="date" name="desde" id="desde" value="{{ $desde }}" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
            </div>

            <div>
                <label for="hasta" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Hasta</label>
                <input type="date" name="hasta" id="hasta" value="{{ $hasta }}" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Estado</label>
                <select name="status" id="status" onchange="this.form.submit()"
                    class="px-3 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
                    <option value="todos" {{ $status == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="in_process" {{ $status == 'in_process' ? 'selected' : '' }}>En Proceso (Check-in)</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <a href="{{ route('barber.export', array_filter(['barber_id' => request('barber_id'), 'desde' => $desde, 'hasta' => $hasta, 'status' => $status])) }}"
               class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Exportar CSV
            </a>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tarjetas Informativas / Métricas del Barbero --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Tarjeta 1: Total Citas --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Citas ({{ \Carbon\Carbon::parse($desde)->format('d/m') }}–{{ \Carbon\Carbon::parse($hasta)->format('d/m') }})</span>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ $totalCitas }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $citasPendientes }} pendientes / {{ $citasCompletadas }} completadas</div>
        </div>

        {{-- Tarjeta 2: Servicios Completados --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Completadas</span>
                <div class="p-2 bg-green-50 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ $citasCompletadas }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Servicios finalizados con éxito</div>
        </div>

        {{-- Tarjeta 3: Comisión Ganada (40%) --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mi Comisión (40%)</span>
                <div class="p-2 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">${{ number_format($gananciaBarbero, 2) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total facturado: ${{ number_format($totalFacturado, 2) }}</div>
        </div>

        {{-- Tarjeta 4: Propinas --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Propinas</span>
                <div class="p-2 bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-purple-600 dark:text-purple-400">${{ number_format($totalPropinas, 2) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Acumuladas en el rango</div>
        </div>
    </div>

    {{-- Tabla / Agenda de Citas del Barbero --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden mb-8 transition-colors">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-900">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Agenda y Lista de Citas ({{ $selectedBarber ? $selectedBarber->name : 'Barbero' }})
            </h2>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-slate-800 px-3 py-1 rounded-full border border-gray-200 dark:border-slate-600">
                {{ $appointments->count() }} cita(s) encontrada(s)
            </span>
        </div>

        @if($appointments->isEmpty())
            <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-base font-semibold text-gray-600 dark:text-gray-300">No hay citas programadas para el filtro seleccionado</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Intenta seleccionar otra fecha o ajustar los filtros superiores.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-slate-900 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3">Hora / Fecha</th>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Servicio</th>
                            <th class="px-6 py-3">Precio / Método</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3 text-right">Acciones Rápidas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700 text-sm">
                        @foreach($appointments as $appointment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                    <div class="text-base font-bold text-gray-900 dark:text-gray-100">
                                        {{ $appointment->appointment_date->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $appointment->appointment_date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $appointment->client?->name ?? 'Cliente General' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $appointment->client?->phone ?? 'Sin teléfono' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $appointment->service?->name ?? 'Corte general' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">${{ number_format($appointment->service?->price ?? 0, 2) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $appointment->payment_method ? $appointment->payment_method : 'No especificado' }}
                                        @if($appointment->tip > 0)
                                            <span class="text-purple-600 dark:text-purple-400 font-semibold">(+${{ number_format($appointment->tip, 2) }} propina)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($appointment->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                Pendiente
                                            </span>
                                            @break
                                        @case('confirmed')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                Confirmada
                                            </span>
                                            @break
                                        @case('in_process')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 animate-pulse">
                                                En Proceso (Check-in)
                                            </span>
                                            @break
                                        @case('completed')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800">
                                                Completada
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-400 border border-red-200 dark:border-red-800">
                                                Cancelada
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-gray-300">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Botón Check-in / En proceso --}}
                                        @if($appointment->status !== 'in_process' && $appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                            <form method="POST" action="{{ route('barber.appointments.updateStatus', $appointment->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="in_process">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition shadow-sm">
                                                    Check-in
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Botón Completar --}}
                                        @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                            <form method="POST" action="{{ route('barber.appointments.updateStatus', $appointment->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition shadow-sm">
                                                    Completar
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Modal de opciones de estado --}}
                                        <button onclick="abrirModalEstado({{ $appointment->id }}, '{{ $appointment->status }}', '{{ $appointment->payment_method }}', '{{ $appointment->tip }}')"
                                            class="border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 text-xs font-medium px-2.5 py-1.5 rounded-lg transition">
                                            Opciones
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Sección Historial de Clientes del Barbero --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 shadow-sm transition-colors">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Historial de Clientes Atendidos
        </h3>

        @if($clientesRecientes->isEmpty())
            <p class="text-gray-400 dark:text-gray-500 text-sm py-4 text-center">Aún no hay registro de clientes completados para este barbero.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($clientesRecientes as $citaRealizada)
                    <div class="bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg p-4 flex flex-col justify-between transition-colors">
                        <div>
                            <div class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-1">{{ $citaRealizada->client?->name ?? 'Cliente' }}</div>
                            <div class="text-xs text-amber-700 dark:text-amber-400 font-semibold mb-2">{{ $citaRealizada->service?->name }}</div>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 pt-2 border-t border-gray-200 dark:border-slate-700 flex justify-between">
                            <span>{{ $citaRealizada->appointment_date->format('d/m/Y H:i') }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">${{ number_format($citaRealizada->service?->price ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- MODAL DE CAMBIO DE ESTADO Y DETALLES --}}
<div id="modal-estado" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-md w-full p-6 shadow-xl relative transition-colors">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Actualizar Cita</h3>

        <form id="form-modal-estado" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="modal-status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Estado de la Cita</label>
                <select name="status" id="modal-status" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg p-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
                    <option value="pending">Pendiente</option>
                    <option value="confirmed">Confirmada</option>
                    <option value="in_process">En Proceso (Check-in)</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="modal-payment" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Método de Pago</label>
                <select name="payment_method" id="modal-payment" class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg p-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
                    <option value="">Seleccionar método</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Transferencia">Transferencia</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="modal-tip" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Propina ($)</label>
                <input type="number" step="0.50" min="0" name="tip" id="modal-tip" placeholder="0.00"
                    class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg p-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModalEstado()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg text-sm transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-gray-900 hover:bg-gray-800 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-semibold rounded-lg text-sm transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalEstado(appointmentId, status, paymentMethod, tip) {
        const form = document.getElementById('form-modal-estado');
        form.action = `/barber/appointments/${appointmentId}/status`;

        document.getElementById('modal-status').value = status || 'pending';
        document.getElementById('modal-payment').value = paymentMethod || '';
        document.getElementById('modal-tip').value = tip || 0;

        document.getElementById('modal-estado').classList.remove('hidden');
    }

    function cerrarModalEstado() {
        document.getElementById('modal-estado').classList.add('hidden');
    }
</script>
@endsection
