<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use App\Services\WapiService;
use App\Jobs\SendAppointmentReminder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function create()
    {
        $services = Service::all();
        $barbers  = Barber::with('user')->get();

        if (auth()->user()->isUser()) {
            return view('cliente.reservar', compact('services', 'barbers'));
        }

        $clientes = User::where('role', 'user')->orderBy('name')->get();

        return view('appointments.create', compact('services', 'barbers', 'clientes'));
    }

    public function store (Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'           => 'required|date|after_or_equal:today',
            'hour'           => 'required|integer|between:1,12',
            'minute'         => 'required|integer|between:0,59',
            'period'         => 'required|in:AM,PM',
            'service_id'     => 'required|exists:services,id',
            'barber_id'      => 'required|exists:users,id',
            'client_id'      => 'nullable|exists:users,id',
            'payment_method' => 'nullable|in:Efectivo,Tarjeta,Transferencia',
            'tip'            => 'nullable|numeric|min:0',
        ]);

        $clientId = auth()->user()->isUser()
            ? auth()->id()
            : ($validated['client_id'] ?? null);

        if (! $clientId) {
            return response()->json(['success' => false, 'message' => 'Debes seleccionar un cliente.'], 422);
        }

        // Formato de 24 horas
        $hour = (int)$validated['hour'];
        if ($validated['period'] === 'PM' && $hour !== 12) {
            $hour +=12;
           } elseif ($validated['period'] === 'AM' && $hour === 12) {
            $hour = 0; 
        }

        $appointmentDate = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['date'] . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($validated['minute'], 2, '0', STR_PAD_LEFT)
        );

        $appointment = Appointment::create([
            'client_id'      => $clientId,
            'barber_id'      => $validated['barber_id'],
            'service_id'     => $validated['service_id'],
            'appointment_date' => $appointmentDate,
            'status'         => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'tip'            => $validated['tip'] ?? 0,
        ]);

        $appointment->load(['client', 'barber', 'service']);

        // Confirmación inmediata
        try {
            $wapi    = new WapiService();
            $client  = $appointment->client;
            $barber  = $appointment->barber;
            $service = $appointment->service;
            $fecha   = $appointmentDate->format('d/m/Y');
            $hora    = $appointmentDate->format('H:i');

            if ($client?->phone) {
                $wapi->sendMessage($client->phone,
                    "✅ *Cita Confirmada - BarberPro*\n\n" .
                    "Hola {$client->name}, tu cita ha sido agendada.\n\n" .
                    "📅 Fecha: {$fecha}\n" .
                    "🕐 Hora: {$hora}\n" .
                    "✂️ Servicio: {$service->name}\n" .
                    "👤 Barbero: {$barber->name}\n\n" .
                    "Te enviaremos un recordatorio 1 hora antes. ¡Hasta pronto!"
                );
            }
        } catch (\Exception $e) {
        }

        // Recordatorio 1 hora antes (si esa ventana ya pasó pero la cita sigue en el futuro, se manda de inmediato)
        $reminderAt = $appointmentDate->copy()->subHour();
        if ($appointmentDate->isFuture()) {
            if ($reminderAt->isFuture()) {
                SendAppointmentReminder::dispatch($appointment)->delay($reminderAt);
            } else {
                SendAppointmentReminder::dispatch($appointment);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cita agendada exitosamente',
            'appointment' => $appointment,
        ]);
    }
}
