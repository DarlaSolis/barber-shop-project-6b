@extends('layouts.public')

@section('title', 'Reservar Cita')

@section('content')
<div class="space-y-6">
    {{-- TÍTULO PRINCIPAL --}}
    <div class="text-center max-w-xl mx-auto">
        <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full text-xs font-semibold uppercase tracking-wider">
            Reserva Fácil & Rápida
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-3 tracking-tight">Agenda tu Cita en BarberPro</h1>
        <p class="text-slate-400 text-sm mt-2">Selecciona tu sucursal, servicio y barbero preferido en menos de 1 minuto sin necesidad de crear cuenta.</p>
    </div>

    {{-- TARJETA PRINCIPAL DE RESERVA --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <form id="publicAppointmentForm" onsubmit="submitPublicAppointment(event)" class="space-y-8">
            @csrf

            {{-- PASO 1: SUCURSAL --}}
            <div>
                <label for="branch_id" class="block text-xs font-bold text-amber-400 uppercase tracking-wider mb-2">
                    1. Selecciona Sucursal
                </label>
                <select id="branch_id" name="branch_id" onchange="checkPublicFormValidity()"
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} — {{ $branch->address }}</option>
                    @endforeach
                </select>
            </div>

            {{-- PASO 2: DATOS DEL CLIENTE --}}
            <div class="border-t border-slate-800 pt-6">
                <label class="block text-xs font-bold text-amber-400 uppercase tracking-wider mb-3">
                    2. Tus Datos de Contacto
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cliente_nombre" class="block text-xs text-slate-400 mb-1">Nombre Completo *</label>
                        <input type="text" id="cliente_nombre" name="name" required placeholder="Ej. Juan Pérez" oninput="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="cliente_email" class="block text-xs text-slate-400 mb-1">Correo Electrónico *</label>
                        <input type="email" id="cliente_email" name="email" required placeholder="juan@ejemplo.com" oninput="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="cliente_telefono" class="block text-xs text-slate-400 mb-1">Teléfono (WhatsApp) *</label>
                        <input type="tel" id="cliente_telefono" name="phone" required placeholder="5551234567" oninput="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            {{-- PASO 3: SERVICIO Y BARBERO --}}
            <div class="border-t border-slate-800 pt-6">
                <label class="block text-xs font-bold text-amber-400 uppercase tracking-wider mb-3">
                    3. Servicio y Barbero
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="service_id" class="block text-xs text-slate-400 mb-1">Servicio Deseado *</label>
                        <select id="service_id" name="service_id" required onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Selecciona un servicio --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} — ${{ number_format($service->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="barber_id" class="block text-xs text-slate-400 mb-1">Barbero de Preferencia *</label>
                        <select id="barber_id" name="barber_id" required onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Selecciona un barbero --</option>
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->user_id }}">{{ $barber->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- PASO 4: FECHA, HORA Y PAGO --}}
            <div class="border-t border-slate-800 pt-6">
                <label class="block text-xs font-bold text-amber-400 uppercase tracking-wider mb-3">
                    4. Horario y Método de Pago
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
                    <div class="sm:col-span-2">
                        <label for="date" class="block text-xs text-slate-400 mb-1">Fecha de la Cita *</label>
                        <input type="date" id="date" name="date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="hour" class="block text-xs text-slate-400 mb-1">Hora *</label>
                        <select id="hour" name="hour" required onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Hora</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="period" class="block text-xs text-slate-400 mb-1">AM/PM *</label>
                        <select id="period" name="period" required onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="AM">AM</option>
                            <option value="PM" selected>PM</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs text-slate-400 mb-1">Método de Pago Estimado</label>
                        <select id="payment_method" name="payment_method"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Efectivo">Efectivo al finalizar</option>
                            <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
                            <option value="Transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>
                    <div>
                        <label for="minute" class="block text-xs text-slate-400 mb-1">Minutos *</label>
                        <select id="minute" name="minute" required onchange="checkPublicFormValidity()"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="0">00 min</option>
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- BOTÓN PRINCIPAL --}}
            <div class="border-t border-slate-800 pt-6">
                <button type="submit" id="publicSubmitBtn" disabled
                    class="w-full py-4 bg-amber-500 hover:bg-amber-400 disabled:bg-slate-800 disabled:text-slate-500 text-slate-950 font-extrabold text-base rounded-xl transition shadow-lg shadow-amber-500/20 cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Confirmar Reserva Online
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function checkPublicFormValidity() {
    const nombre   = document.getElementById('cliente_nombre').value.trim();
    const email    = document.getElementById('cliente_email').value.trim();
    const phone    = document.getElementById('cliente_telefono').value.trim();
    const service  = document.getElementById('service_id').value;
    const barber   = document.getElementById('barber_id').value;
    const date     = document.getElementById('date').value;
    const hour     = document.getElementById('hour').value;

    const isValid = nombre && email && phone && service && barber && date && hour;
    const btn = document.getElementById('publicSubmitBtn');

    btn.disabled = !isValid;
    btn.classList.toggle('cursor-not-allowed', !isValid);
    btn.classList.toggle('cursor-pointer', isValid);
}

function submitPublicAppointment(event) {
    event.preventDefault();

    const btn = document.getElementById('publicSubmitBtn');
    btn.disabled = true;
    btn.innerText = 'Procesando Cita...';

    // 1. Primero registra/obtiene al cliente
    const nombre = document.getElementById('cliente_nombre').value.trim();
    const email  = document.getElementById('cliente_email').value.trim();
    const phone  = document.getElementById('cliente_telefono').value.trim();

    fetch('{{ route("clientes.quickStore") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ name: nombre, email: email, phone: phone })
    })
    .then(res => res.json())
    .then(clientData => {
        if (!clientData.success && !clientData.client) {
            throw new Error(clientData.message || 'Error registrando cliente');
        }

        const clientId = clientData.client.id;

        // 2. Registrar la Cita
        const formData = {
            client_id: clientId,
            branch_id: document.getElementById('branch_id').value,
            service_id: document.getElementById('service_id').value,
            barber_id: document.getElementById('barber_id').value,
            date: document.getElementById('date').value,
            hour: document.getElementById('hour').value,
            minute: document.getElementById('minute').value,
            period: document.getElementById('period').value,
            payment_method: document.getElementById('payment_method').value,
        };

        return fetch('{{ route("appointments.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(formData)
        });
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Cita Confirmada! ✂️',
                text: 'Hemos agendado tu cita correctamente. Te enviamos la confirmación.',
                icon: 'success',
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Excelente'
            }).then(() => {
                document.getElementById('publicAppointmentForm').reset();
                checkPublicFormValidity();
            });
        } else {
            Swal.fire({
                title: 'No se pudo reservar',
                text: data.message || 'Ocurrió un inconveniente al procesar la cita.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(err => {
        Swal.fire({
            title: 'Atención',
            text: err.message || 'No se pudo completar el registro.',
            icon: 'warning',
            confirmButtonColor: '#f59e0b'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = `✂️ Confirmar Reserva Online`;
        checkPublicFormValidity();
    });
}
</script>
@endsection
