@extends('layouts.admin')

@section('title', 'Reportes')

@section('content')
<div class="max-w-6xl">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Reportes</h1>
        <span class="text-gray-500 dark:text-gray-400 text-sm">Ingresos y servicios más vendidos</span>
    </div>

    {{-- Filtro de fechas --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-6 transition-colors">
        <form method="GET" action="{{ route('reportes.index') }}" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                       class="border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                       class="border border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-semibold px-5 py-2 rounded-lg transition">
                Aplicar
            </button>
        </form>
    </div>

    {{-- Card total --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-8 max-w-xs transition-colors">
        <span class="text-sm text-gray-500 dark:text-gray-400">Ingresos Totales</span>
        <div class="text-3xl font-bold text-gray-800 dark:text-amber-400 mt-1">${{ number_format($total_ingresos, 2) }}</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Gráfica de ingresos por día --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 transition-colors">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Ingresos por Día</h2>
            <canvas id="chartIngresos"></canvas>
        </div>

        {{-- Gráfica servicios más vendidos --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-5 transition-colors">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Servicios Más Vendidos</h2>
            <canvas id="chartServicios"></canvas>
        </div>
    </div>

    {{-- Tabla servicios más vendidos --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 mb-8 transition-colors overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-slate-700">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Servicio</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cantidad</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Ingresos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicios_mas_vendidos as $s)
                    <tr class="border-b border-gray-50 dark:border-slate-700/50 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $s->name }}</td>
                        <td class="px-5 py-3 text-sm text-gray-800 dark:text-gray-100 text-right">{{ $s->cantidad }}</td>
                        <td class="px-5 py-3 text-sm text-amber-600 dark:text-amber-400 font-medium text-right">${{ number_format($s->ingresos, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">No hay datos en este rango</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // JS / Datos de Laravel
    const ingresosDia = @json($ingresos_por_dia);
    const servicios   = @json($servicios_mas_vendidos);

    // Detecta si el modo oscuro está activo para ajustar colores de las gráficas
    const isDarkMode = document.documentElement.classList.contains('dark');
    const gridColor = isDarkMode ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.05)';
    const tickColor = isDarkMode ? '#cbd5e1' : '#374151';

    const chartOptionsBase = {
        scales: {
            x: {
                ticks: { color: tickColor },
                grid: { color: gridColor }
            },
            y: {
                ticks: { color: tickColor },
                grid: { color: gridColor }
            }
        },
        plugins: {
            legend: {
                labels: { color: tickColor }
            }
        }
    };

    new Chart(document.getElementById('chartIngresos'), {
        type: 'line',
        data: {
            labels: ingresosDia.map(i => i.dia),
            datasets: [{
                label: 'Ingresos ($)',
                data: ingresosDia.map(i => i.total),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.1)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: chartOptionsBase
    });

    new Chart(document.getElementById('chartServicios'), {
        type: 'bar',
        data: {
            labels: servicios.map(s => s.name),
            datasets: [{
                label: 'Veces vendido',
                data: servicios.map(s => s.cantidad),
                backgroundColor: isDarkMode ? '#f59e0b' : '#1f2937',
            }]
        },
        options: { ...chartOptionsBase, indexAxis: 'y' }
    });
</script>
@endsection
