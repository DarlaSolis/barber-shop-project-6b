<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ReportesController;

Route::get('/', function () {
    return redirect('/reservar');
});

// Ruta pública exclusiva para clientes de Reserva Online (sin login, sin sidebar)
Route::get('/reservar', [AppointmentController::class, 'publicBooking'])->name('public.booking');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::post('/clientes/quick', [ClienteController::class, 'quickStore'])->name('clientes.quickStore');
Route::get('/api/services', [ServiceController::class, 'index']);

Route::middleware('auth')->group(function () {
    // Común a todos los roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cliente y admin: reservar citas
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');

    // Barbero y admin: panel del barbero
    Route::get('/barber', [BarberController::class, 'index'])->name('barber.index');
    if (method_exists(BarberController::class, 'export')) {
        Route::get('/barber/export', [BarberController::class, 'export'])->name('barber.export');
    }
    Route::put('/barber/appointments/{appointment}/status', [BarberController::class, 'updateStatus'])->name('barber.appointments.updateStatus');

    // Solo Admin: caja, clientes, reportes
    Route::get('/registros', [RegistrosController::class, 'index'])->name('registros.index');
    Route::get('/registros/export', [RegistrosController::class, 'export'])->name('registros.export');
    Route::get('/registros/export-pdf', [RegistrosController::class, 'exportPdf'])->name('registros.exportPdf');
    Route::put('/registros/{appointment}', [RegistrosController::class, 'update'])->name('registros.update');
    Route::delete('/registros/{appointment}', [RegistrosController::class, 'destroy'])->name('registros.destroy');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    if (class_exists(ReportesController::class)) {
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
    }
});

require __DIR__.'/auth.php';
