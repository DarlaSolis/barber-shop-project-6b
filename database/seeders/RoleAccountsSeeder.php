<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Admin hardcodeado [Temporal]
        User::updateOrCreate(
            ['email' => 'admin@barbershop.com'],
            ['name' => 'Administrador', 'password' => Hash::make('Admin123'), 'role' => 'admin']
        );

        // Usuario general hardcodeado [Temporal] - Cliente
        User::updateOrCreate(
            ['email' => 'cliente@gmail.com'],
            ['name' => 'Cliente General', 'phone' => '123456789', 'password' => Hash::make('Cliente123'), 'role' => 'user']
        );
    }
}
