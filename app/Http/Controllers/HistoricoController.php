<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Historico;
use App\Models\User;
use App\Models\Documento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\DocumentoAsignado;


class HistoricoController extends AuthenticatedController
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = Historico::with(['documento', 'usuario', 'destinatarioUser'])
            ->orderBy('fecha_cambio', 'desc');

        // Aplicar filtro por remitente
        if ($request->filled('remitente')) {
            $query->whereHas('documento', function ($q) use ($request) {
                $q->where('remitente', 'like', '%' . $request->remitente . '%');
            });
        }

        // Aplicar filtro por número de oficio
        if ($request->filled('numero_oficio')) {
            $query->whereHas('documento', function ($q) use ($request) {
                $q->where('numero_oficio', 'like', '%' . $request->numero_oficio . '%');
            });
        }

        $historicos = $query->paginate(10); // Paginación
        $users = User::all(); // Lista de usuarios para el combobox

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

    public function asignar2(Request $request, $id)
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

    public function asignar(Request $request, $idDocumento)
    {
        $request->validate([
            'destinatario' => 'nullable|exists:users,id',
            'estado_nuevo' => 'required|string|in:por firma,observado,en proceso,otro',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            // Verificar que el documento existe
            $documento = Documento::findOrFail($idDocumento);

            // Obtener el último estado del histórico (si existe)
            $ultimoHistorico = Historico::where('id_documento', $idDocumento)->latest('fecha_cambio')->first();
            $estadoAnterior = $ultimoHistorico ? $ultimoHistorico->estado_nuevo : 'pendiente';

            // Verificar que estado_anterior no sea NULL
            if (!$estadoAnterior) {
                $estadoAnterior = 'recibido';
            }

            // Crear un nuevo registro en `historicos`
            $historico = Historico::create([
                'id_documento' => $idDocumento, // ID del documento asociado
                'id_usuario' => request()->user()->id, // Usuario que realiza la asignación
                'destinatario' => $request->destinatario_id, // Usuario destinatario
                'estado_anterior' => $estadoAnterior, // Estado anterior o predeterminado
                'estado_nuevo' => $request->estado_nuevo, // Estado nuevo
                'fecha_cambio' => now(), // Fecha del cambio
                'observaciones' => $request->observaciones, // Observaciones opcionales
            ]);

            // Obtener el destinatario
            $destinatario = User::find($request->destinatario);

            // Enviar la notificación al destinatario
            if ($destinatario) {
                $destinatario->notify(new DocumentoAsignado($historico));
            }

            return redirect()->route('documentos.index')->with('success', 'Asignación registrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->with('error', 'Error al asignar destinatario: ' . $e->getMessage());
        }
    }




}