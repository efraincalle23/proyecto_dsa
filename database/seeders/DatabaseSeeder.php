<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Llamar a los seeders necesarios
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Agrega otros seeders aquí si los necesitas
        ]);

        // Crear usuarios base (opcional)
        if (User::count() === 0) { // Evitar duplicados
            $admin = User::create([
                'nombre' => 'admin',
                'apellido' => 'DSA',
                'email' => 'admin@DSA.com',
                'password' => bcrypt('12345678'), // Asegúrate de usar una contraseña fuerte
                'foto' => 'fotos/default.png',
            ]);

            $admin->assignRole('Administrador');

            $user = User::create([
                'nombre' => 'Jefe',
                'apellido' => 'DSA',
                'email' => 'jefe@dsa.com',
                'password' => bcrypt('12345678'), // Contraseña de prueba
                'foto' => 'fotos/default.png',

            ]);

            $user->assignRole('Jefe DSA');
        }
    }
}