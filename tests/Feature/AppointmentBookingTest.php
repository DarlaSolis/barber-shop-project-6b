<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Barber;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_booking_page_can_be_rendered_without_auth(): void
    {
        $response = $this->get('/reservar');

        $response->assertStatus(200);
        $response->assertSee('Agendar');
    }

    public function test_appointment_can_be_stored_via_api(): void
    {
        $client = User::create([
            'name' => 'Cliente Test',
            'email' => 'clientetest@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $barberUser = User::create([
            'name' => 'Barbero Test',
            'email' => 'barberotest@example.com',
            'password' => bcrypt('password'),
            'role' => 'barber',
        ]);

        $barber = Barber::create([
            'user_id' => $barberUser->id,
            'description' => 'Especialista',
            'clients_count' => 0,
        ]);

        $service = Service::create([
            'name' => 'Corte Clásico',
            'price' => 250.00,
        ]);

        $postData = [
            'date' => date('Y-m-d', strtotime('+1 day')),
            'hour' => 10,
            'minute' => 0,
            'period' => 'AM',
            'service_id' => $service->id,
            'barber_id' => $barberUser->id,
            'client_id' => $client->id,
            'payment_method' => 'Efectivo',
            'tip' => 50,
        ];

        $response = $this->postJson('/appointments', $postData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'barber_id' => $barberUser->id,
            'service_id' => $service->id,
            'status' => 'pending',
            'payment_method' => 'Efectivo',
        ]);
    }
}
