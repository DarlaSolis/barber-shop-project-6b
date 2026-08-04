@extends('layouts.admin')

@section('title', 'Cobros y Pagos')

@section('content')
<div class="max-w-6xl">

  {{-- Header --}}
<div class="flex flex-nowrap items-center justify-between gap-4 mb-6">

    <div class="min-w-0">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Cobros y Pagos</h1>
        <span class="text-gray-500 dark:text-gray-400 text-sm">Registro completo de transacciones</span>
    </div>

    <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('registros.exportPdf', request()->only(['barber_id','metodo'])) }}"
           class="flex items-center gap-2 whitespace-nowrap bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-500 text-white font-semibold px-5 py-2 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6M9 17h3"/>
            </svg>
            Exportar PDF
        </a>

        <a href="{{ route('registros.export', request()->only(['barber_id','metodo'])) }}"
           class="flex items-center gap-2 whitespace-nowrap bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-500 text-white font-semibold px-5 py-2 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Exportar excel
        </a>
    </div>
</div>

    {{-- Stat Cards --}}
    <div class="flex gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 flex-1 flex justify-between items-start transition-colors">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Cobrado</span>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">${{ number_format($total_cobrado, 2) }}</div>
            </div>
            <div class="bg-gray-100 dark:bg-slate-700 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 flex-1 flex justify-between items-start transition-colors">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Comisiones Barbería</span>
                <div class="text-2xl font-bold text-gray-800 dark:text-amber-400 mt-1">${{ number_format($total_comisiones, 2) }}</div>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">60% del total</span>
            </div>
            <div class="bg-gray-100 dark:bg-slate-700 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-10 10M7 7h10v10"/>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 flex-1 flex justify-between items-start transition-colors">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Ganancias Barberos</span>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">${{ number_format($ganancias_barberos, 2) }}</div>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">40% del total</span>
            </div>
            <div class="bg-gray-100 dark:bg-slate-700 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-6 transition-colors">
        <div class="flex justify-between items-center gap-6 flex-wrap">
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide block mb-2">Método de Pago</span>
                @php $metodoActual = request('metodo', 'todos'); @endphp
                <div class="flex gap-2 flex-wrap">
                    @foreach(['todos' => 'Todos los métodos', 'Efectivo' => 'Efectivo', 'Tarjeta' => 'Tarjeta', 'Transferencia' => 'Transferencia'] as $val => $label)
                        <a href="{{ route('registros.index', array_merge(request()->only('barber_id'), ['metodo' => $val])) }}"
                           class="flex items-center gap-1 text-sm px-4 py-1.5 rounded-full transition
                               {{ $metodoActual === $val ? 'bg-amber-500 dark:bg-amber-600 text-white' : 'border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wide block mb-2">Barbero</span>
                <form method="GET" action="{{ route('registros.index') }}">
                    <select name="barber_id" onchange="this.form.submit()"
                        class="border border-gray-200 dark:border-slate-600 dark:bg-slate-900 text-gray-700 dark:text-white text-sm rounded-lg px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500">
                        <option value="">Todos los barberos</option>
                        @foreach($barbers as $barber)
                            <option value="{{ $barber->user_id }}" {{ request('barber_id') == $barber->user_id ? 'selected' : '' }}>
                                {{ $barber->user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 transition-colors overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Fecha</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Cliente</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Barbero</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Servicio</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Método</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Total</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Comisión</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Barbero</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                    @php
                        $precio    = $appointment->service->price ?? 0;
                        $comision  = $precio * 0.60;
                        $ganancia  = $precio * 0.40;
                    @endphp
                    <tr class="border-b border-gray-50 dark:border-slate-700/50 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-5 py-3">
                            <div class="text-sm text-gray-800 dark:text-gray-100">{{ $appointment->appointment_date->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $appointment->appointment_date->format('H:i') }}</div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $appointment->client->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $appointment->barber->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $appointment->service->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $appointment->payment_method ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-800 dark:text-gray-100 text-right">${{ number_format($precio, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-amber-500 dark:text-amber-400 font-medium text-right">${{ number_format($comision, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200 text-right">${{ number_format($ganancia, 2) }}</td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="abrirEditar({{ $appointment->id }}, '{{ $appointment->appointment_date->format('Y-m-d\TH:i') }}', {{ $appointment->service_id }}, {{ $appointment->barber_id }}, '{{ $appointment->payment_method }}', '{{ $appointment->status }}')"
                                    class="text-xs border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    Editar
                                </button>
                                <button onclick="confirmarEliminar({{ $appointment->id }})"
                                    class="text-xs border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 px-3 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">No hay registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Modal Editar --}}
<div id="modal-editar" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Editar Registro</h2>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">&times;</button>
        </div>
        <form id="form-editar" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Fecha y Hora</label>
                    <input type="datetime-local" name="appointment_date" id="edit-fecha"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Servicio</label>
                    <select name="service_id" id="edit-service"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500">
                        @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} — ${{ number_format($s->price,2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Barbero</label>
                    <select name="barber_id" id="edit-barber"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500">
                        @foreach($barbers as $b)
                            <option value="{{ $b->user_id }}">{{ $b->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Método de Pago</label>
                    <select name="payment_method" id="edit-metodo"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500">
                        <option value="">— Sin especificar —</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="edit-status"
                        class="w-full border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-500">
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="completed">Completado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
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

{{-- Form eliminar oculto --}}
<form id="form-eliminar" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function abrirEditar(id, fecha, serviceId, barberId, metodo, status) {
        document.getElementById('form-editar').action = '/registros/' + id;
        document.getElementById('edit-fecha').value   = fecha;
        document.getElementById('edit-service').value = serviceId;
        document.getElementById('edit-barber').value  = barberId;
        document.getElementById('edit-metodo').value  = metodo || '';
        document.getElementById('edit-status').value  = status;
        document.getElementById('modal-editar').classList.remove('hidden');
    }

    function confirmarEliminar(id) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el registro permanentemente.',
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
                const form = document.getElementById('form-eliminar');
                form.action = '/registros/' + id;
                form.submit();
            }
        });
    }

    document.getElementById('modal-editar').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endsection
