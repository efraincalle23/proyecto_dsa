<?php

namespace App\Http\Controllers;


use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::query();  // No es necesario con 'with('rol')'

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('rol')) {
            $query->where('rol', 'like', '%' . $request->rol . '%');  // Filtrar por el campo 'rol'
        }

        $usuarios = $query->paginate(10); // Pagina los resultados

        return view('usuarios.index', compact('usuarios'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validar la foto
        ]);

        try {
            // Si no se sube una foto, asignamos una foto por defecto
            $fotoPath = 'fotos/default.png'; // Ruta de la foto por defecto

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('fotos', 'public'); // Guardamos la foto en el directorio 'storage/app/public/fotos'
            }

            Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
                'foto' => $fotoPath // Guardamos la ruta de la foto
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        // Validación de los campos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
            'rol' => 'required|string|max:255',
            'password' => 'nullable|min:8',  // La contraseña es opcional y si se proporciona debe tener al menos 8 caracteres
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validación de la foto
        ]);

        try {
            // Mantenemos la foto actual si no se sube una nueva
            $fotoPath = $usuario->foto; // Foto actual

            if ($request->hasFile('foto')) {
                // Si se sube una nueva foto, la eliminamos la antigua y subimos la nueva
                if ($fotoPath && $fotoPath != 'fotos/default.png') {
                    Storage::disk('public')->delete($fotoPath); // Eliminar foto anterior
                }

                // Guardamos la nueva foto
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            // Actualizamos los demás campos
            $usuario->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'rol' => $request->rol,
                'password' => $request->filled('password') ? bcrypt($request->password) : $usuario->password, // Si no se proporciona nueva contraseña, mantenemos la actual
                'foto' => $fotoPath // Guardamos la foto, ya sea la nueva o la antigua
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }



    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = Usuario::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }

}