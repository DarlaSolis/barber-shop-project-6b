<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function handle(WapiService $wapi): void
    {
        $client = $this->appointment->client;
        $barber = $this->appointment->barber;
        $service = $this->appointment->service;

        if (!$client?->phone) return;

        $fecha = $this->appointment->appointment_date->format('d/m/Y');
        $hora  = $this->appointment->appointment_date->format('H:i');

        $wapi->sendMessage($client->phone,
            "⏰ *Recordatorio de Cita - BarberPro*\n\n" .
            "Hola {$client->name}, tu cita es en 1 hora.\n\n" .
            "📅 Fecha: {$fecha}\n" .
            "🕐 Hora: {$hora}\n" .
            "✂️ Servicio: {$service->name}\n" .
            "👤 Barbero: {$barber->name}\n\n" .
            "¡Te esperamos!"
        );
    }
}
