<?php
/**
 * Test Runner Script para BarberPro
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Service;
use App\Models\Barber;
use App\Models\Appointment;

echo "===============================================\n";
echo "       EJECUTANDO PRUEBAS DEL SISTEMA         \n";
echo "===============================================\n\n";

$passed = 0;
$failed = 0;

function assertTest($condition, $title) {
    global $passed, $failed;
    if ($condition) {
        echo "✅ [PASS] $title\n";
        $passed++;
    } else {
        echo "❌ [FAIL] $title\n";
        $failed++;
    }
}

// PRUEBA 1: Evaluación de Roles
$admin = new User(['role' => 'admin']);
assertTest($admin->isAdminGeneral(), 'Evaluación de Rol: Admin General (admin)');

$barber = new User(['role' => 'barber']);
assertTest($barber->isBarber(), 'Evaluación de Rol: Barbero (barber)');

$cliente = new User(['role' => 'user']);
assertTest($cliente->isCliente(), 'Evaluación de Rol: Cliente (user)');

// PRUEBA 2: Consulta de Barberos y Servicios
$servicesCount = Service::count();
assertTest($servicesCount >= 0, "Catálogo de Servicios accesible (Total: $servicesCount)");

$barbersCount = Barber::count();
assertTest($barbersCount >= 0, "Catálogo de Barberos accesible (Total: $barbersCount)");

// PRUEBA 3: Creación simulada de cita
$clientTest = User::firstOrCreate(['email' => 'unittest@example.com'], ['name' => 'Test Client', 'password' => bcrypt('12345678'), 'role' => 'user']);
$barberTest = User::firstWhere('role', 'barber') ?? User::create(['email' => 'barbertest@example.com', 'name' => 'Barber Test', 'password' => bcrypt('12345678'), 'role' => 'barber']);
$serviceTest = Service::first() ?? Service::create(['name' => 'Corte Test', 'price' => 200]);

$appointment = Appointment::create([
    'client_id' => $clientTest->id,
    'barber_id' => $barberTest->id,
    'service_id' => $serviceTest->id,
    'appointment_date' => now(),
    'status' => 'pending',
    'payment_method' => 'Efectivo',
]);

assertTest($appointment->id > 0, "Creación de Cita exitosa (ID Cita: {$appointment->id})");

// PRUEBA 4: Check-in / Cambio de Estado de Cita
$appointment->update(['status' => 'in_process']);
assertTest($appointment->fresh()->status === 'in_process', "Check-in de Cita actualizado a 'in_process'");

echo "\n===============================================\n";
echo "RESULTADOS DE LAS PRUEBAS:\n";
echo "Pasadas: $passed | Falladas: $failed\n";
echo "===============================================\n";
