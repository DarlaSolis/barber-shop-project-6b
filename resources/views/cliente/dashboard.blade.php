@extends('layouts.admin')
@section('title', 'Mi Panel')

@section('content')
<div class="max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mis Citas</h1>
        <a href="{{ route('appointments.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2 rounded-lg">
            + Reservar Cita
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barbero</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Servicio</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($misCitas as $c)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3 text-sm text-gray-800">{{ $c->appointment_date->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $c->barber->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $c->service->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ ucfirst($c->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">No tienes citas registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
