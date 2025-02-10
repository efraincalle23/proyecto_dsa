<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSessionController extends Controller
{
    public function disconnect($id)
    {
        // Actualizar la tabla user_sessions
        DB::table('user_sessions')
            ->where('user_id', $id)
            ->update(['is_active' => false]);

        // Si el usuario desconectado es el usuario actual, cerrar sesión
        if (Auth::id() == $id) {
            Auth::logout();  // Cierra la sesión de Laravel
            Session::flush(); // Limpia todas las sesiones activas del usuario
            return redirect('/login')->with('success', 'Se ha cerrado tu sesión.');
        }

        return redirect()->back()->with('success', 'Usuario desconectado exitosamente.');
    }
}