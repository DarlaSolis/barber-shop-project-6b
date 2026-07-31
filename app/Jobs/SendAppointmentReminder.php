<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function handle(TwilioService $twilio): void
    {
        $client = $this->appointment->client;
        $barber = $this->appointment->barber;
        $service = $this->appointment->service;

        if (!$client?->phone) return;

        $fecha = $this->appointment->appointment_date->format('d/m/Y');
        $hora  = $this->appointment->appointment_date->format('H:i');

        $twilio->sendTemplate($client->phone, env('TWILIO_REMINDER_SID'), [
            '1' => $fecha,
            '2' => $hora,
        ]);
    }
}
