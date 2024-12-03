<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'Nombre' => 'required|max:255',
            'Apellido' => 'required|max:255',
            'Email' => 'required|email|unique:usuarios,Email',
            'Contrasena' => 'required|min:6',
        ]);

        // Crear el usuario
        Usuario::create([
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'Email' => $request->Email,
            'Contrasena' => bcrypt($request->Contrasena),
        ]);

        return redirect()->route('usuarios.index');
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $request->validate([
            'Nombre' => 'required|max:255',
            'Apellido' => 'required|max:255',
            'Email' => 'required|email',
        ]);

        // Encontrar y actualizar el usuario
        $usuario = Usuario::findOrFail($id);
        $usuario->update([
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'Email' => $request->Email,
        ]);

        return redirect()->route('usuarios.index');
    }

    public function destroy($id)
    {
        // Eliminar el usuario
        Usuario::destroy($id);

        return redirect()->route('usuarios.index');
    }



}