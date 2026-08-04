@extends('layouts.admin')

@section('title', 'Agendar Cita')

@section('content')
<div class="max-w-8xl">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Agendar Nueva Cita</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- PRIMER CONTAINER: Seleccionar / Crear Cliente --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-slate-700 transition-colors">
        <h3 class="text-xl font-bold text-gray-800 dark:text-amber-400 mb-4 flex items-center gap-2">👤 Seleccionar Cliente</h3>

        <input type="hidden" id="client_id" name="client_id" value="">

        <div class="space-y-4">
            {{-- Cliente seleccionado --}}
            <div id="cliente-seleccionado" class="hidden border border-amber-300 dark:border-amber-500 bg-amber-50 dark:bg-slate-900/80 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div id="sel-iniciales" class="w-8 h-8 rounded-full bg-amber-200 dark:bg-amber-600 text-amber-800 dark:text-white flex items-center justify-center text-xs font-bold flex-shrink-0"></div>
                        <div id="sel-nombre" class="text-sm font-bold text-gray-800 dark:text-gray-100"></div>
                    </div>
                    <button type="button" onclick="limpiarCliente()" class="text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">× Cambiar</button>
                </div>
            </div>

            {{-- Búsqueda + lista --}}
            <input type="text" id="buscar-cliente-cita" placeholder="Buscar cliente por nombre..."
                class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 transition"/>

            <div id="lista-clientes-cita" class="hidden border border-gray-200 dark:border-slate-700 rounded-lg divide-y divide-gray-100 dark:divide-slate-700 max-h-48 overflow-y-auto bg-white dark:bg-slate-900 shadow-lg">
                @forelse($clientes as $cliente)
                    @php
                        $p = explode(' ', trim($cliente->name));
                        $ini = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
                    @endphp
                    <div class="cliente-cita-item flex items-center gap-3 px-4 py-2.5 hover:bg-amber-50 dark:hover:bg-slate-800 cursor-pointer transition"
                         data-id="{{ $cliente->id }}"
                         data-nombre="{{ $cliente->name }}"
                         data-iniciales="{{ $ini }}"
                         data-search="{{ strtolower($cliente->name) }}">
                        <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-800 text-amber-700 dark:text-amber-200 flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $ini }}</div>
                        <div>
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $cliente->name }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-400">{{ $cliente->email }}</div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-4 text-center text-gray-400 dark:text-gray-500 text-sm">No hay clientes registrados</div>
                @endforelse
            </div>

            {{-- Nuevo cliente rápido --}}
            <div class="border-t border-gray-100 dark:border-slate-700 pt-4 space-y-3">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">+ Nuevo Cliente Rápido</p>
                <input type="text" id="nuevo-nombre" placeholder="Nombre completo"
                    class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"/>
                <input type="email" id="nuevo-email" placeholder="Correo electrónico"
                    class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"/>
                <input type="text" id="nuevo-phone" placeholder="Teléfono (opcional)"
                    class="w-full border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"/>
                <button type="button" onclick="crearCliente()"
                    class="w-full bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold py-2.5 rounded-lg transition shadow-md">
                    Registrar y Seleccionar
                </button>
                <p id="nuevo-error" class="text-xs text-red-500 hidden"></p>
            </div>
        </div>
    </div>

    <!-- Fecha y Hora -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-slate-700 transition-colors">
            <h3 class="text-xl font-bold text-gray-800 dark:text-amber-400 mb-4 flex items-center gap-2">📅 Selecciona Fecha y Hora</h3>

            <form id="appointmentForm" class="space-y-5">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">Fecha de la Cita</label>
                    <input type="date" id="date" name="date" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" min="{{ date('Y-m-d') }}">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-xs">Horas</label>
                        <select id="hour" name="hour" required class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Hora</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-xs">Minutos</label>
                        <select id="minute" name="minute" required class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Minuto</option>
                            @for($i = 0; $i <= 50; $i += 10)
                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-xs">Período</label>
                        <select id="period" name="period" required class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">AM/PM</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-xs">Método de pago</label>
                        <select id="payment_method" name="payment_method" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Selecciona</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-xs">Propina (opcional)</label>
                        <input type="text" id="tip" name="tip" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="$0.00">
                    </div>
                </div>
            </form>
        </div>

    <!-- Servicios + Barberos + Sucursal -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-slate-700 transition-colors">
            <h3 class="text-xl font-bold text-gray-800 dark:text-amber-400 mb-4 flex items-center gap-2">✂️ Sucursal, Servicio y Barbero</h3>

            <div class="space-y-5">
                @if(isset($branches) && count($branches) > 0)
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">Sucursal</label>
                    <select id="branch_id" name="branch_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Sucursal Principal --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">Tipo de Servicio</label>
                    <select id="service_id" name="service_id" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" onchange="checkFormValidity()">
                        <option value="">-- Selecciona un servicio --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }} - ${{ number_format($service->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">Selecciona Barbero</label>
                    <select id="barber_id" name="barber_id" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" onchange="checkFormValidity()">
                        <option value="">-- Selecciona un barbero --</option>
                        @foreach($barbers as $barber)
                            <option value="{{ $barber->user_id }}">{{ $barber->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button id="submitBtn" type="submit" form="appointmentForm" class="w-full bg-amber-600 hover:bg-amber-500 disabled:bg-gray-400 dark:disabled:bg-slate-700 text-white font-bold py-3.5 px-4 rounded-xl transition cursor-not-allowed shadow-md" disabled onclick="submitAppointment(event)">
                    Confirmar y Reservar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function checkFormValidity() {
    const date     = document.getElementById('date').value;
    const hour     = document.getElementById('hour').value;
    const minute   = document.getElementById('minute').value;
    const period   = document.getElementById('period').value;
    const serviceId = document.getElementById('service_id').value;
    const barberId  = document.getElementById('barber_id').value;
    const clientId  = document.getElementById('client_id').value;

    const isValid = date && hour && minute !== '' && period && serviceId && barberId && clientId;

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = !isValid;
    submitBtn.classList.toggle('cursor-not-allowed', !isValid);
    submitBtn.classList.toggle('cursor-pointer', isValid);
}

document.getElementById('date').addEventListener('change', checkFormValidity);
document.getElementById('hour').addEventListener('change', checkFormValidity);
document.getElementById('minute').addEventListener('change', checkFormValidity);
document.getElementById('period').addEventListener('change', checkFormValidity);
document.getElementById('service_id').addEventListener('change', checkFormValidity);

// Seleccionar cliente de la lista
document.querySelectorAll('.cliente-cita-item').forEach(function(el) {
    el.addEventListener('click', function() {
        seleccionarCliente(this.dataset.id, this.dataset.nombre, this.dataset.iniciales);
        document.querySelectorAll('.cliente-cita-item').forEach(e => e.classList.remove('bg-amber-100', 'dark:bg-amber-900/50'));
        this.classList.add('bg-amber-100', 'dark:bg-amber-900/50');
    });
});

function seleccionarCliente(id, nombre, iniciales) {
    document.getElementById('client_id').value = id;
    document.getElementById('sel-iniciales').textContent = iniciales;
    document.getElementById('sel-nombre').textContent = nombre;
    document.getElementById('cliente-seleccionado').classList.remove('hidden');
    checkFormValidity();
}

function limpiarCliente() {
    document.getElementById('client_id').value = '';
    document.getElementById('cliente-seleccionado').classList.add('hidden');
    document.querySelectorAll('.cliente-cita-item').forEach(e => e.classList.remove('bg-amber-100', 'dark:bg-amber-900/50'));
    checkFormValidity();
}

// Buscar cliente en lista
document.getElementById('buscar-cliente-cita').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    const lista = document.getElementById('lista-clientes-cita');

    if (!q) {
        lista.classList.add('hidden');
        return;
    }

    let hayResultados = false;
    document.querySelectorAll('.cliente-cita-item').forEach(function(el) {
        const match = el.dataset.search.includes(q);
        el.style.display = match ? '' : 'none';
        if (match) hayResultados = true;
    });

    lista.classList.toggle('hidden', !hayResultados);
});

document.getElementById('buscar-cliente-cita').addEventListener('blur', function() {
    setTimeout(() => document.getElementById('lista-clientes-cita').classList.add('hidden'), 150);
});

document.getElementById('buscar-cliente-cita').addEventListener('focus', function() {
    if (this.value.trim()) document.getElementById('lista-clientes-cita').classList.remove('hidden');
});

// Crear cliente rápido vía AJAX
function crearCliente() {
    const nombre = document.getElementById('nuevo-nombre').value.trim();
    const email  = document.getElementById('nuevo-email').value.trim();
    const phone  = document.getElementById('nuevo-phone').value.trim();
    const err    = document.getElementById('nuevo-error');

    if (!nombre || !email) {
        err.textContent = 'Nombre y email son requeridos.';
        err.classList.remove('hidden');
        return;
    }

    fetch('{{ route("clientes.quickStore") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ name: nombre, email: email, phone: phone }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            err.classList.add('hidden');
            const c = data.client;
            const p = c.name.split(' ');
            const ini = (p[0][0] + (p[1] ? p[1][0] : '')).toUpperCase();

            // Agregar a lista
            const lista = document.getElementById('lista-clientes-cita');
            const div = document.createElement('div');
            div.className = 'cliente-cita-item flex items-center gap-3 px-4 py-2.5 hover:bg-amber-50 dark:hover:bg-slate-800 cursor-pointer transition bg-amber-100 dark:bg-amber-900/50';
            div.dataset.id = c.id;
            div.dataset.nombre = c.name;
            div.dataset.iniciales = ini;
            div.dataset.search = c.name.toLowerCase();
            div.innerHTML = `<div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800 text-amber-700 dark:text-amber-200 flex items-center justify-center text-xs font-bold flex-shrink-0">${ini}</div><div><div class="text-sm font-semibold text-gray-800 dark:text-gray-200">${c.name}</div><div class="text-xs text-gray-400">${c.email}</div></div>`;
            div.addEventListener('click', function() {
                seleccionarCliente(this.dataset.id, this.dataset.nombre, this.dataset.iniciales);
                document.querySelectorAll('.cliente-cita-item').forEach(e => e.classList.remove('bg-amber-100', 'dark:bg-amber-900/50'));
                this.classList.add('bg-amber-100', 'dark:bg-amber-900/50');
            });
            lista.prepend(div);

            seleccionarCliente(c.id, c.name, ini);
            document.getElementById('nuevo-nombre').value = '';
            document.getElementById('nuevo-email').value = '';
            document.getElementById('nuevo-phone').value = '';
        } else {
            err.textContent = data.message || 'Error al crear cliente.';
            err.classList.remove('hidden');
        }
    })
    .catch(() => {
        err.textContent = 'Error de conexión.';
        err.classList.remove('hidden');
    });
}

function submitAppointment(event) {
    event.preventDefault();

    const formData = {
        date:           document.getElementById('date').value,
        hour:           document.getElementById('hour').value,
        minute:         document.getElementById('minute').value,
        period:         document.getElementById('period').value,
        service_id:     document.getElementById('service_id').value,
        barber_id:      document.getElementById('barber_id').value,
        branch_id:      document.getElementById('branch_id') ? document.getElementById('branch_id').value : null,
        client_id:      document.getElementById('client_id').value,
        payment_method: document.getElementById('payment_method').value,
        tip:            document.getElementById('tip').value,
    };

    fetch('{{ route("appointments.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(formData),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Cita agendada!',
                text: 'La cita fue reservada exitosamente.',
                icon: 'success',
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Aceptar',
            }).then(() => {
                document.getElementById('appointmentForm').reset();
                limpiarCliente();
                checkFormValidity();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo agendar la cita.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
            });
        }
    })
    .catch(() => Swal.fire({ title: 'Error', text: 'Error de conexión.', icon: 'error' }));
}

checkFormValidity();
</script>
@endsection