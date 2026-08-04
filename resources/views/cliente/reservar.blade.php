@extends('layouts.admin')

@section('title', 'Reservar Cita')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Reservar una Cita</h1>
    <p class="text-gray-500 mb-8">Elige tu servicio, barbero y horario. Recibirás la confirmación por WhatsApp.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Tipo de Servicio</label>
                <select id="service_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">-- Selecciona un servicio --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }} — ${{ number_format($service->price, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Barbero</label>
                <select id="barber_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">-- Selecciona un barbero --</option>
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->user_id }}">{{ $barber->user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Fecha</label>
                <input type="date" id="date" min="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">Hora</label>
                    <select id="hour" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">--</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">Min</label>
                    <select id="minute" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">--</option>
                        @for($i = 0; $i <= 50; $i += 10)
                            <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">Período</label>
                    <select id="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">--</option>
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button id="submitBtn" disabled onclick="reservar()" class="bg-yellow-500 hover:bg-yellow-600 disabled:bg-gray-300 text-white font-bold py-3 px-8 rounded-lg transition">
            Reservar Cita
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const ids = ['service_id', 'barber_id', 'date', 'hour', 'minute', 'period'];

    function validar() {
        const ok = ids.every(id => document.getElementById(id).value !== '');
        document.getElementById('submitBtn').disabled = !ok;
    }
    ids.forEach(id => document.getElementById(id).addEventListener('change', validar));

    function reservar() {
        const payload = {
            service_id: document.getElementById('service_id').value,
            barber_id:  document.getElementById('barber_id').value,
            date:       document.getElementById('date').value,
            hour:       document.getElementById('hour').value,
            minute:     document.getElementById('minute').value,
            period:     document.getElementById('period').value,
        };

        fetch('{{ route("appointments.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ title: '¡Cita reservada!', text: 'Te esperamos. Revisa tu WhatsApp.', icon: 'success', confirmButtonColor: '#f59e0b' })
                    .then(() => window.location = '{{ route("dashboard") }}');
            } else {
                Swal.fire({ title: 'Error', text: data.message || 'No se pudo reservar.', icon: 'error', confirmButtonColor: '#ef4444' });
            }
        })
        .catch(() => Swal.fire({ title: 'Error', text: 'Error de conexión.', icon: 'error' }));
    }
</script>
@endsection
