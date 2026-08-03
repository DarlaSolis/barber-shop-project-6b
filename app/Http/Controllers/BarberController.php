<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    /**
     * Muestra la vista principal del rol de barbero con su agenda, métricas y gestión de citas.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Restringir acceso si el usuario es cliente normal
        if ($user && $user->role === 'user') {
            return redirect()->route('appointments.create')->with('error', 'No tienes permisos para acceder al panel del barbero.');
        }

        // Obtener todos los barberos registrados para el selector
        $barbers = Barber::with('user')->get();

        // Determinar qué barbero está seleccionado
        if ($request->filled('barber_id')) {
            $selectedBarberId = $request->barber_id;
        } elseif ($user && $user->isBarber()) {
            $selectedBarberId = $user->id;
        } else {
            $selectedBarberId = $barbers->first()?->user_id ?? ($user?->id);
        }

        $selectedBarber = User::find($selectedBarberId);

        // Fecha del filtro (por defecto hoy)
        $selectedDate = $request->input('fecha', Carbon::today()->format('Y-m-d'));

        // Construir consulta de citas para el barbero seleccionado
        $query = Appointment::with(['client', 'service', 'barber'])
            ->where('barber_id', $selectedBarberId);

        // Filtro por fecha si no es 'todas'
        if ($request->input('filtro_fecha') !== 'todas') {
            $query->whereDate('appointment_date', $selectedDate);
        }

        // Filtro por estado si se especifica
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

        // Calcular estadísticas para el barbero en el día seleccionado
        $citasHoyQuery = Appointment::where('barber_id', $selectedBarberId)
            ->whereDate('appointment_date', $selectedDate);

        $totalCitas = $citasHoyQuery->count();
        
        $completedAppointments = (clone $citasHoyQuery)
            ->where('status', 'completed')
            ->with('service')
            ->get();

        $citasCompletadas = $completedAppointments->count();
        $citasPendientes = (clone $citasHoyQuery)->whereIn('status', ['pending', 'confirmed', 'in_process'])->count();
        
        $totalFacturado = $completedAppointments->sum(fn($a) => $a->service?->price ?? 0);
        $gananciaBarbero = $totalFacturado * 0.40; // 40% comisión para el barbero
        $totalPropinas = $completedAppointments->sum('tip');

        // Historial reciente de clientes atendidos por este barbero
        $clientesRecientes = Appointment::where('barber_id', $selectedBarberId)
            ->with('client', 'service')
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->limit(8)
            ->get();

        return view('barber.index', compact(
            'barbers',
            'selectedBarber',
            'selectedBarberId',
            'selectedDate',
            'appointments',
            'totalCitas',
            'citasCompletadas',
            'citasPendientes',
            'totalFacturado',
            'gananciaBarbero',
            'totalPropinas',
            'clientesRecientes'
        ));
    }

    /**
     * Actualiza el estado de una cita (Check-in, Completar, Cancelar) o su cobro/propina.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status'         => 'required|in:pending,confirmed,in_process,completed,cancelled',
            'payment_method' => 'nullable|in:Efectivo,Tarjeta,Transferencia',
            'tip'            => 'nullable|numeric|min:0',
        ]);

        $data = ['status' => $validated['status']];

        if ($request->has('payment_method') && $validated['payment_method']) {
            $data['payment_method'] = $validated['payment_method'];
        }

        if ($request->has('tip')) {
            $data['tip'] = $validated['tip'] ?? 0;
        }

        $appointment->update($data);

        $statusLabels = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'in_process' => 'En Proceso (Check-in realizado)',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ];

        $mensaje = 'Estado de la cita actualizado a "' . ($statusLabels[$validated['status']] ?? $validated['status']) . '".';

        return redirect()->back()->with('success', $mensaje);
    }
}
