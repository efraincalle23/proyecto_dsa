<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AuthenticatedController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth'); // Aplica el middleware a todas las rutas de este controlador
    }
    public function dashboard()
    {
        $rol = request()->user()->rol; // Ya está autenticado
        return view('dashboard', compact('rol'));
    }
}