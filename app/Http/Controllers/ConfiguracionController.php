<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ConfiguracionController extends Controller
{
    public function actualizarNumeroOficioInicio(Request $request)
    {
        $request->validate([
            'numero_oficio_inicio' => 'required|integer|min:1'
        ]);
    
        DB::table('configuraciones')
            ->where('clave', 'numero_oficio_inicio')
            ->update(['valor' => $request->numero_oficio_inicio]);
    
        return back()->with('success', 'Número de inicio actualizado correctamente.');
    }
    
}