<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Historico;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistoricoController extends AuthenticatedController
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $historicos = Historico::with(['documento', 'usuario', 'destinatarioUser'])->paginate(10);
        $users = User::all(); // Cargar todos los usuarios

        return view('historicos.index', compact('historicos', 'users'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function asignar(Request $request, $id)
    {
        $request->validate([
            'destinatario' => 'nullable|exists:users,id',
            'estado_nuevo' => 'required|string|in:por firma,observado,en proceso,otro',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            $historico = Historico::findOrFail($id);

            Historico::create([
                'id_documento' => $historico->id_documento,
                'id_usuario' => request()->user()->id,
                'destinatario' => $request->destinatario,
                'estado_anterior' => $historico->estado_nuevo ?? $historico->estado_anterior,
                'estado_nuevo' => $request->estado_nuevo,
                'fecha_cambio' => now(),
                'observaciones' => $request->observaciones,
            ]);

            return redirect()->route('historicos.index')->with('success', 'Registro actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('historicos.index')->with('error', 'Error al asignar destinatario: ' . $e->getMessage());
        }
    }
}