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
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->role === 'user') {
            return redirect()->route('appointments.create')->with('error', 'No tienes permisos para acceder al panel del barbero.');
        }

        $barbers = Barber::with('user')->get();

        if ($request->filled('barber_id')) {
            $selectedBarberId = $request->barber_id;
        } elseif ($user && $user->isBarber()) {
            $selectedBarberId = $user->id;
        } else {
            $selectedBarberId = $barbers->first()?->user_id ?? ($user?->id);
        }

        $selectedBarber = User::find($selectedBarberId);
        $selectedDate = $request->input('fecha', Carbon::today()->format('Y-m-d'));

        $query = Appointment::with(['client', 'service', 'barber'])
            ->where('barber_id', $selectedBarberId);

        if ($request->input('filtro_fecha') !== 'todas') {
            $query->whereDate('appointment_date', $selectedDate);
        }

        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

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
        $gananciaBarbero = $totalFacturado * 0.40;
        $totalPropinas = $completedAppointments->sum('tip');

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

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Refuerzo: un barbero solo puede tocar SUS citas (el admin puede todas)
        if ($request->user()->isBarber() && $appointment->barber_id !== $request->user()->id) {
            abort(403, 'No puedes modificar citas de otro barbero.');
        }

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
