<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class AssignRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ejemplo: Asignar roles a usuarios específicos
        $user1 = User::find(2); // Usuario con ID 1
        $user1->assignRole('Jefe DSA');

        $user2 = User::find(1); // Usuario con ID 2
        $user2->assignRole('Administrador');

        //$user3 = User::find(6); // Usuario con ID 3
        //$user3->assignRole('Secretaria');

        $user4 = User::find(3); // Usuario con ID 4
        $user4->assignRole('Administrativo');
    }
}