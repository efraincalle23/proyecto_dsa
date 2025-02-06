<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends AuthenticatedController
{
    public function __construct()
    {
        // Middleware de autenticación general para todas las funciones
        $this->middleware('auth');

        // Aplicar el middleware de rol solo a la función 'index'
        $this->middleware(function ($request, $next) {
            if (!request()->user()->hasAnyRole(['Administrador', 'Jefe DSA'])) {
                abort(403, 'Acceso denegado');
            }
            return $next($request);
        })->only(['index']);  // Aplica solo a la función 'index'
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = User::query();  // No es necesario con 'with('rol')'

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('rol')) {
            $query->where('rol', 'like', '%' . $request->rol . '%');  // Filtrar por el campo 'rol'
        }

        $users = $query->paginate(10); // Pagina los resultados

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Cambiado de 'usuarios' a 'users'
            'password' => 'required|string|min:8',
            'rol' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validar la foto
        ]);

        try {
            // Si no se sube una foto, asignamos una foto por defecto
            $fotoPath = 'assets/images/default.png'; // Ruta de la foto por defecto

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('fotos', 'public'); // Guardamos la foto en el directorio 'storage/app/public/fotos'
            }

            User::create([ // Cambiado de Usuario a User
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
                'foto' => $fotoPath // Guardamos la ruta de la foto
            ]);

            return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // Puedes usar la inyección de modelo si lo prefieres
    {
        $user = User::findOrFail($id); // Cambiado de Usuario a User
        return view('users.profile', compact('user')); // Cambiado de 'usuarios.show' a 'users.show'
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //funcional
        $user = User::findOrFail($id); // Cambiado de Usuario a User

        // Validación de los campos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Cambiado de 'usuarios' a 'users' y ajustado el identificador
            'rol' => 'required|string|max:255',
            'password' => 'nullable|min:8',  // La contraseña es opcional y si se proporciona debe tener al menos 8 caracteres
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validación de la foto
        ]);

        try {
            // Mantenemos la foto actual si no se sube una nueva
            $fotoPath = $user->foto; // Foto actual

            if ($request->hasFile('foto')) {
                // Si se sube una nueva foto, eliminamos la antigua y subimos la nueva
                if ($fotoPath && $fotoPath != 'assets/images/default.png') {
                    Storage::disk('public')->delete($fotoPath); // Eliminar foto anterior
                }

                // Guardamos la nueva foto
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            // Actualizamos los demás campos
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->rol = $request->rol;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->foto = $fotoPath; // Guardamos la foto, ya sea la nueva o la antigua

            $user->save(); // Guardamos los cambios

            return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function updateFoto(Request $request, $id)
    {
        //funcional
        $user = User::findOrFail($id); // Cambiado de Usuario a User

        // Validación de los campos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Cambiado de 'usuarios' a 'users' y ajustado el identificador
            'rol' => 'required|string|max:255',
            'password' => 'nullable|min:8',  // La contraseña es opcional y si se proporciona debe tener al menos 8 caracteres
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Validación de la foto
        ]);

        try {
            // Mantenemos la foto actual si no se sube una nueva
            $fotoPath = $user->foto; // Foto actual

            if ($request->hasFile('foto')) {
                // Si se sube una nueva foto, eliminamos la antigua y subimos la nueva
                if ($fotoPath && $fotoPath != 'assets/images/default.png') {
                    Storage::disk('public')->delete($fotoPath); // Eliminar foto anterior
                }

                // Guardamos la nueva foto
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            // Actualizamos los demás campos
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->rol = $request->rol;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->foto = $fotoPath; // Guardamos la foto, ya sea la nueva o la antigua

            $user->save(); // Guardamos los cambios

            return redirect()->route('users.profile')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function updatefuncionaenmodal(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'rol' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
                }

                if ($request->new_password !== $request->new_password_confirmation) {
                    return back()->withErrors(['new_password' => 'Las contraseñas nuevas no coinciden']);
                }

                if (strlen($request->new_password) < 8) {
                    return back()->withErrors(['new_password' => 'La contraseña debe tener al menos 8 caracteres']);
                }

                $user->password = Hash::make($request->new_password);
            }

            $fotoPath = $user->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath && $fotoPath != 'fotos/default.png') {
                    Storage::disk('public')->delete($fotoPath);
                }
                $fotoPath = $request->file('foto')->store('fotos', 'public');
            }

            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->rol = $request->rol;
            $user->foto = $fotoPath;
            $user->save();

            return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['general' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        // Validación de los campos
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',  // Confirmación de nueva contraseña
        ]);

        // Obtener el usuario
        $user = User::find($id);

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            // Si no es correcta, redirigir con un error
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'La contraseña se ha actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id); // Cambiado de Usuario a User
            $user->delete();

            return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /*Verificar si un email ya existe.
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = User::where('email', $email)->exists(); // Cambiado de Usuario a User

        return response()->json(['exists' => $exists]);
    }
}