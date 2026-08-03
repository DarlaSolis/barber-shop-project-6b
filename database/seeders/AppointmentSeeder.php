<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos usuarios clientes si no existen
        $client1 = User::create([
            'name' => 'Roberto Gómez',
            'email' => 'roberto@example.com',
            'phone' => '555-0101',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $client2 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana@example.com',
            'phone' => '555-0102',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $client3 = User::create([
            'name' => 'Luis Fernando',
            'email' => 'luis@example.com',
            'phone' => '555-0103',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        $firstBarberUser = User::where('role', 'barber')->first();
        $services = Service::all();

        if ($firstBarberUser && $services->count() >= 2) {
            $today = Carbon::today();

            // Cita 1: Completada con propina
            Appointment::create([
                'client_id' => $client1->id,
                'barber_id' => $firstBarberUser->id,
                'service_id' => $services[0]->id,
                'appointment_date' => $today->copy()->setHour(9)->setMinute(0),
                'status' => 'completed',
                'payment_method' => 'Efectivo',
                'tip' => 5.00
            ]);

            // Cita 2: En proceso (Check-in)
            Appointment::create([
                'client_id' => $client2->id,
                'barber_id' => $firstBarberUser->id,
                'service_id' => $services[1]->id,
                'appointment_date' => $today->copy()->setHour(10)->setMinute(30),
                'status' => 'in_process',
                'payment_method' => 'Tarjeta',
                'tip' => 0
            ]);

            // Cita 3: Confirmada para más tarde
            Appointment::create([
                'client_id' => $client3->id,
                'barber_id' => $firstBarberUser->id,
                'service_id' => $services[0]->id,
                'appointment_date' => $today->copy()->setHour(12)->setMinute(0),
                'status' => 'confirmed',
                'payment_method' => null,
                'tip' => 0
            ]);
        }
    }
}
