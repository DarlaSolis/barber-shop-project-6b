<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function checkin(Appointment $appointment)
    {
        abort_unless($appointment->barber_id === auth()->id(), 403);

        $appointment->update(['status' => 'confirmed']);

        return back()->with('success', 'Cita confirmada con éxito.');
        
    }
}
