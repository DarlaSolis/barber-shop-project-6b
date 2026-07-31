<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas (por defecto: mes actual)
        $desde = $request->desde ?? now()->startOfMonth()->toDateString();
        $hasta = $request->hasta ?? now()->endOfMonth()->toDateString();

    // Base -> Citas en el rango con estado
    $base = Appointment::query()
    ->whereBetween('appointment_date', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
    ->where('status', 'completed'); // Ajustar a completado si aplica

    
    // Reporte de ingresos0
    // Total general (suma del precio del servicio de cada cita)
    $total_ingresos = (clone $base)
    ->join('services', 'appointments.service_id', '=', 'services.id')
    ->sum('services.price');

    // Ingresos por método de pago
    $ingresos_por_metodo = (clone $base)
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->select('payment_method', DB::raw('SUM(services.price) as total'), DB::raw('COUNT(*) as citas'))
        ->groupBy('payment_method')
        ->get();

    // Ingresos por día (para la gráfica de líneas)
    $ingresos_por_dia = (clone $base)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(DB::raw('DATE(appointment_date) as dia'), DB::raw('SUM(services.price) as total'))
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

    // Servicios más vendidos
    $servicios_mas_vendidos = (clone $base)
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->select(
            'services.name',
            DB::raw('COUNT(*) as cantidad'),
            DB::raw('SUM(services.price) as ingresos')
        )
        ->groupBy('services.id', 'services.name')
        ->orderByDesc('cantidad')
        ->get();

    return view('reportes.index', compact(
        'desde',
        'hasta',
        'total_ingresos',
        'ingresos_por_metodo',
        'ingresos_por_dia',
        'servicios_mas_vendidos'
    ));
    }

}



