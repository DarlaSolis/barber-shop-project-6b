@extends('layouts.admin')
@section('title', 'Mi agenda')

@section('content')
<div class="max-w-5xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Mi Agenda de Hoy</h1>
    <p class="text-gray-500 mb-6">Comisión acumulada del día: <span class="font-bold text-amber-600">${{ number_format($comisionDia, 2) }}</span></p>

    <div class="bg-white rounded-lg border border-gray-200">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hora</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Servicio</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Check-in</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($citas as $c)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3 text-sm text-gray-800">{{ $c->appointment_date->format('H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $c->client->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $c->service->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ ucfirst($c->status) }}</td>
                        <td class="px-5 py-3 text-center">
                            @if ($c->status === 'pending')
                                <form method="POST" action="{{ route('barber.checkin', $c) }}">
                                    @csrf @method('PUT')
                                    <button class="text-xs bg-gray-900 text-white px-3 py-1 rounded-lg hover:bg-gray-700">Check-in</button>
                                </form>
                            @else
                                <span class="text-xs text-green-600 font-medium">✓ Listo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">No tienes citas hoy</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
                    
                                    