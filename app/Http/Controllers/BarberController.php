<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarberController extends Controller
{
     // El barbero verá sus citas y la comisión que le corresponde, y el admin gestionará esta información.
    private function filtros(Request $request, ?User $user): array
    {
        $barbers = Barber::with('user')->get();

        if ($user && $user->isBarber()) {
            $selectedBarberId = $user->id;                      
        } elseif ($request->filled('barber_id')) {
            $selectedBarberId = $request->barber_id;             
        } else {
            $selectedBarberId = $barbers->first()?->user_id ?? ($user?->id);
        }

        // Rango de fechas
        $desde  = $request->input('desde', Carbon::today()->format('Y-m-d'));
        $hasta  = $request->input('hasta', Carbon::today()->format('Y-m-d'));
        $status = $request->input('status', 'todos');

        return [$selectedBarberId, $desde, $hasta, $status, $barbers];
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->role === 'user') {
            return redirect()->route('appointments.create')->with('error', 'No tienes permisos para acceder al panel del barbero.');
        }

        [$selectedBarberId, $desde, $hasta, $status, $barbers] = $this->filtros($request, $user);

        $selectedBarber = User::find($selectedBarberId);

        // Citas de la tabla
        $query = Appointment::with(['client', 'service', 'barber'])
            ->where('barber_id', $selectedBarberId)
            ->whereBetween('appointment_date', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        if ($status && $status !== 'todos') {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

        // Métricas sobre TODO el rango
        $rangoQuery = Appointment::where('barber_id', $selectedBarberId)
            ->whereBetween('appointment_date', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        $totalCitas = (clone $rangoQuery)->count();

        $completedAppointments = (clone $rangoQuery)
            ->where('status', 'completed')
            ->with('service')
            ->get();

        $citasCompletadas = $completedAppointments->count();
        $citasPendientes  = (clone $rangoQuery)->whereIn('status', ['pending', 'confirmed', 'in_process'])->count();

        $totalFacturado  = $completedAppointments->sum(fn($a) => $a->service?->price ?? 0);
        $gananciaBarbero = $totalFacturado * 0.40;
        $totalPropinas   = $completedAppointments->sum('tip');

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
            'desde',
            'hasta',
            'status',
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

    public function export(Request $request)
    {
        $user = $request->user();

        if ($user && $user->role === 'user') {
            abort(403);
        }

        [$selectedBarberId, $desde, $hasta, $status, $barbers] = $this->filtros($request, $user);

        $query = Appointment::with(['client', 'service'])
            ->where('barber_id', $selectedBarberId)
            ->whereBetween('appointment_date', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        if ($status && $status !== 'todos') {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();
        $barbero = User::find($selectedBarberId);

        $filename = 'reporte_' . str_replace(' ', '_', strtolower($barbero->name ?? 'barbero')) . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($appointments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Fecha', 'Cliente', 'Servicio', 'Estado', 'Método', 'Precio', 'Propina', 'Comisión (40%)']);

            foreach ($appointments as $a) {
                $precio   = $a->service->price ?? 0;
                $comision = $a->status === 'completed' ? $precio * 0.40 : 0; // solo cuenta lo cobrado
                fputcsv($handle, [
                    $a->appointment_date->format('d/m/Y H:i'),
                    $a->client->name ?? '—',
                    $a->service->name ?? '—',
                    $a->status,
                    $a->payment_method ?? '—',
                    number_format($precio, 2),
                    number_format($a->tip ?? 0, 2),
                    number_format($comision, 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        
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
            'pending'    => 'Pendiente',
            'confirmed'  => 'Confirmada',
            'in_process' => 'En Proceso (Check-in realizado)',
            'completed'  => 'Completada',
            'cancelled'  => 'Cancelada',
        ];

        $mensaje = 'Estado de la cita actualizado a "' . ($statusLabels[$validated['status']] ?? $validated['status']) . '".';

        return redirect()->back()->with('success', $mensaje);
    }
}
