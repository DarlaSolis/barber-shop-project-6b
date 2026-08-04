<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Barber;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarberPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_barber_can_access_barber_panel(): void
    {
        $barberUser = User::create([
            'name' => 'Barbero Panel',
            'email' => 'barberpanel@example.com',
            'password' => bcrypt('password'),
            'role' => 'barber',
        ]);

        Barber::create([
            'user_id' => $barberUser->id,
            'description' => 'Experto en barba',
            'clients_count' => 0,
        ]);

        $response = $this->actingAs($barberUser)->get('/barber');

        $response->assertStatus(200);
        $response->assertSee('Panel del Barbero');
    }

    public function test_barber_can_update_appointment_status_and_check_in(): void
    {
        $barberUser = User::create([
            'name' => 'Barbero Panel 2',
            'email' => 'barberpanel2@example.com',
            'password' => bcrypt('password'),
            'role' => 'barber',
        ]);

        $client = User::create([
            'name' => 'Cliente CheckIn',
            'email' => 'checkin@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $service = Service::create([
            'name' => 'Corte y Barba',
            'price' => 350.00,
        ]);

        $appointment = Appointment::create([
            'client_id' => $client->id,
            'barber_id' => $barberUser->id,
            'service_id' => $service->id,
            'appointment_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($barberUser)->put("/barber/appointments/{$appointment->id}/status", [
            'status' => 'in_process',
            'payment_method' => 'Efectivo',
            'tip' => 20,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'in_process',
            'payment_method' => 'Efectivo',
            'tip' => 20,
        ]);
    }
}
