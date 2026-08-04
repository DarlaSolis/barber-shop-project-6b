<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_user_roles_evaluation(): void
    {
        $admin = new User(['role' => 'admin']);
        $this->assertTrue($admin->isAdminGeneral());

        $adminGen = new User(['role' => 'admin_general']);
        $this->assertTrue($adminGen->isAdminGeneral());

        $encargado = new User(['role' => 'encargado_sucursal']);
        $this->assertTrue($encargado->isEncargado());

        $barber = new User(['role' => 'barber']);
        $this->assertTrue($barber->isBarber());

        $cliente = new User(['role' => 'user']);
        $this->assertTrue($cliente->isCliente());
    }
}
