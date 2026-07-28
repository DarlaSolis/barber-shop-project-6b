<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Cobros y Pagos</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 11px; margin: 0; }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 20px; margin: 0 0 2px; color: #1f2937; }
        .header .sub { color: #6b7280; font-size: 10px; }
        .header .meta { color: #9ca3af; font-size: 9px; margin-top: 4px; }

        .cards { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .cards td { width: 33.33%; border: 1px solid #e5e7eb; padding: 10px 12px; vertical-align: top; }
        .cards .label { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .cards .value { font-size: 16px; font-weight: bold; margin-top: 3px; }
        .cards .note { color: #9ca3af; font-size: 8px; margin-top: 2px; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            background: #f9fafb; color: #4b5563; font-size: 9px; text-transform: uppercase;
            text-align: left; padding: 7px 8px; border-bottom: 1px solid #e5e7eb;
        }
        table.data td { padding: 7px 8px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        table.data .right { text-align: right; }
        table.data .amber { color: #f59e0b; font-weight: bold; }
        .empty { text-align: center; color: #9ca3af; padding: 20px; }
        tfoot td { font-weight: bold; border-top: 2px solid #e5e7eb; padding: 8px; font-size: 10px; }
        .footer { margin-top: 20px; text-align: center; color: #9ca3af; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cobros y Pagos</h1>
        <div class="sub">Registro completo de transacciones</div>
        <div class="meta">
            Generado: {{ now()->format('d/m/Y H:i') }}
            @if($filtroBarbero) &nbsp;|&nbsp; Barbero: {{ $filtroBarbero }} @endif
            @if($filtroMetodo) &nbsp;|&nbsp; Método: {{ $filtroMetodo }} @endif
        </div>
    </div>

    <table class="cards">
        <tr>
            <td>
                <div class="label">Total Cobrado</div>
                <div class="value">${{ number_format($total_cobrado, 2) }}</div>
            </td>
            <td>
                <div class="label">Comisiones Barbería</div>
                <div class="value">${{ number_format($total_comisiones, 2) }}</div>
                <div class="note">60% del total</div>
            </td>
            <td>
                <div class="label">Ganancias Barberos</div>
                <div class="value">${{ number_format($ganancias_barberos, 2) }}</div>
                <div class="note">40% del total</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Barbero</th>
                <th>Servicio</th>
                <th>Método</th>
                <th class="right">Total</th>
                <th class="right">Comisión</th>
                <th class="right">Barbero</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $a)
                @php
                    $precio   = $a->service->price ?? 0;
                    $comision = $precio * 0.60;
                    $ganancia = $precio * 0.40;
                @endphp
                <tr>
                    <td>{{ $a->appointment_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $a->client->name ?? '—' }}</td>
                    <td>{{ $a->barber->name ?? '—' }}</td>
                    <td>{{ $a->service->name ?? '—' }}</td>
                    <td>{{ $a->payment_method ?? '—' }}</td>
                    <td class="right">${{ number_format($precio, 2) }}</td>
                    <td class="right amber">${{ number_format($comision, 2) }}</td>
                    <td class="right">${{ number_format($ganancia, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No hay registros</td></tr>
            @endforelse
        </tbody>
        @if($appointments->count())
            <tfoot>
                <tr>
                    <td colspan="5" class="right">Totales</td>
                    <td class="right">${{ number_format($total_cobrado, 2) }}</td>
                    <td class="right">${{ number_format($total_comisiones, 2) }}</td>
                    <td class="right">${{ number_format($ganancias_barberos, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">Barber Shop — Reporte generado automáticamente</div>
</body>
</html>
