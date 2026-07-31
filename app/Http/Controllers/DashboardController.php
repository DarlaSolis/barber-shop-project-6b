<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Dashboard Admin
        if ($user->isAdmin()) {
            $barbers = Barber::with('user')->get();
            $now = Carbon::now();
            $selectedBarberId = request('barber_id');

            $query = Appointment::whereYear('appointment_date', $now->year)
                ->whereMonth('appointment_date', $now->month)
                ->with(['client', 'barber', 'service']);

            if ($selectedBarberId) {
                $query->where('barber_id', $selectedBarberId);
            }

            $todaysAppointments = $query->orderBy('appointment_date')->get();

            return view('dashboard.index', compact('barbers', 'todaysAppointments', 'selectedBarberId'));
        }

        // El barbero verá sus citas y la comisión que le corresponde por el día
        if ($user->isBarber()) {
            $citas = Appointment::with(['client', 'service'])
                ->where('barber_id', $user->id)
                ->whereDate('appointment_date', Carbon::today())
                ->orderBy('appointment_date')
                ->get();

            $comisionDia = $citas->where('status', 'completed')
                ->sum(fn ($a) => ($a->service->price ?? 0) * 0.40);

            return view('barbero.dashboard', compact('citas', 'comisionDia'));
        }

        // Historial donde el cliente vea sus citas
        $misCitas = Appointment::with(['barber', 'service'])
            ->where('client_id', $user->id)
            ->orderByDesc('appointment_date')
            ->get();

        return view('cliente.dashboard', compact('misCitas'));
    }
}