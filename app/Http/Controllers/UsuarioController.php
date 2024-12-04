<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    // Muestra la lista de usuarios
    public function index()
    {
        $usuarios = Usuario::with('rol')->get(); // Obtiene usuarios con sus roles
        $roles = Rol::all(); // Obtiene todos los roles para el formulario de creación y edición
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    // Muestra el formulario de creación de usuario
    public function create()
    {
        $roles = Rol::all(); // Obtiene todos los roles
        return view('usuarios.create', compact('roles'));
    }

    // Guarda un nuevo usuario
    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed', // Confirmación de contraseña
            'id_rol' => 'required|exists:roles,id_rol', // Verifica que el rol exista
        ]);

        // Crear el usuario
        $usuario = new Usuario();
        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password); // Encriptar la contraseña
        $usuario->id_rol = $request->id_rol;
        $usuario->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    // Muestra el formulario de edición del usuario
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id); // Busca el usuario por ID
        $roles = Rol::all(); // Obtiene todos los roles
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    // Actualiza los datos del usuario
    public function update(Request $request, $id)
    {
        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $id, // Excluye el usuario actual
            'password' => 'nullable|string|min:6|confirmed', // La contraseña puede ser opcional al editar
            'id_rol' => 'required|exists:roles,id_rol', // Verifica que el rol exista
        ]);

        // Actualiza el usuario
        $usuario = Usuario::findOrFail($id);
        $usuario->nombre = $request->nombre;
        $usuario->apellido = $request->apellido;
        $usuario->email = $request->email;
        if ($request->password) {
            $usuario->password = bcrypt($request->password); // Si se proporciona nueva contraseña, la encripta
        }
        $usuario->id_rol = $request->id_rol;
        $usuario->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    // Elimina un usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        // Redirigir con mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }


}