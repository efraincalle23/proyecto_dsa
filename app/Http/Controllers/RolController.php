<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller
{
    //
    public function insertarRoles()
    {
        // Insertar roles
        Rol::create(['id_rol' => 1, 'nombre_rol' => 'Jefa DSA']);
        Rol::create(['id_rol' => 2, 'nombre_rol' => 'Administrador']);
        Rol::create(['id_rol' => 3, 'nombre_rol' => 'Administrativo']);

        // Retornar un mensaje de éxito
        return response()->json(['mensaje' => 'Roles insertados correctamente']);
    }
}