<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BarberController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Vista Rol de Barbero
    Route::get('/barber', [BarberController::class, 'index'])->name('barber.index');
    Route::put('/barber/appointments/{appointment}/status', [BarberController::class, 'updateStatus'])->name('barber.appointments.updateStatus');
    
    // Agendar Citas
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    
    // API para servicios
    Route::get('/api/services', [ServiceController::class, 'index']);
    
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/registros', [RegistrosController::class, 'index'])->name('registros.index');
    Route::get('/registros/export', [RegistrosController::class, 'export'])->name('registros.export');
    Route::get('/registros/export-pdf', [RegistrosController::class, 'exportPdf'])->name('registros.exportPdf');
    Route::put('/registros/{appointment}', [RegistrosController::class, 'update'])->name('registros.update');
    Route::delete('/registros/{appointment}', [RegistrosController::class, 'destroy'])->name('registros.destroy');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::post('/clientes/quick', [ClienteController::class, 'quickStore'])->name('clientes.quickStore');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
});

require __DIR__.'/auth.php';
