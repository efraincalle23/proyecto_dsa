<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'ver documentos',
            'editar documentos',
            'eliminar documentos',
            'asignar documentos',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $roles = [
            'Administrador' => $permissions, // Todos los permisos
            'Jefe DSA' => $permissions, // Todos los permisos
            'Administrativo' => ['ver documentos'], // Solo ver documentos
            'Secretaria' => ['ver documentos', 'editar documentos', 'asignar documentos'] // Permisos específicos
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}