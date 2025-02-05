<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\User;

class RolesPermisosTestController extends Controller
{
    //
    public function index()
    {
        $usuarios = User::all(); // Obtener todos los usuarios
        $documentos = Documento::all(); // Obtener todos los documentos

        return view('test.roles_permisos_test', compact('usuarios', 'documentos'));
    }
}