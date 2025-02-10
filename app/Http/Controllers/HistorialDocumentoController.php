<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DocumentoRecibido;
use App\Models\DocumentoEmitido;
use App\Models\HistorialDocumento;
use App\Notifications\DocumentoAsignado;
use App\Notifications\DocumentoRecibidoAsignado;

class HistorialDocumentoController extends Controller
{
    //
    public function index(Request $request)
    {

        $query = HistorialDocumento::with([
            'documento',        // Relación con documentos recibidos
            'documentoEmitido', // Relación con documentos emitidos
            'usuario',          // Usuario que hizo el cambio
            'destinatarioUser'  // Usuario destinatario (si aplica)
        ]);// Paginación

        // Filtrar por número de oficio
        if ($request->filled('numero_oficio')) {
            $query->whereHas('documento', function ($q) use ($request) {
                $q->where('numero_oficio', 'like', '%' . $request->numero_oficio . '%');
            })->orWhereHas('documentoEmitido', function ($q) use ($request) {
                $q->where('numero_oficio', 'like', '%' . $request->numero_oficio . '%');
            });
        }

        // Filtrar por fecha de cambio
        if ($request->filled('fecha_cambio')) {
            $query->whereDate('fecha_cambio', $request->fecha_cambio);
        }

        // Filtrar por remitente o destino
        if ($request->filled('remitente_destino')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('documento', function ($subQuery) use ($request) {
                    $subQuery->where('remitente', 'like', '%' . $request->remitente_destino . '%');
                })->orWhereHas('documentoEmitido', function ($subQuery) use ($request) {
                    $subQuery->where('destino', 'like', '%' . $request->remitente_destino . '%');
                });
            });
        }
        $historial = $query->paginate(10);
        $historicos = HistorialDocumento::query()->paginate(10); // Paginación
        $users = User::all(); // Lista de usuarios para el combobox

        return view('historicos.index', compact('historicos', 'users', 'historial'));
    }

    public function asignar(Request $request, $idDocumento)
    {
        $request->validate([
            'destinatario' => 'nullable|exists:users,id',
            'estado_nuevo' => 'required|string',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            $origen = 'recibido'; // Puedes cambiar esto según la lógica que desees

            // Verificar que el documento existe
            $documento = DocumentoRecibido::findOrFail($idDocumento);

            // Obtener el último estado del histórico (si existe)
            $ultimoHistorico = HistorialDocumento::where('id_documento', $idDocumento)->latest('fecha_cambio')->first();
            $estadoAnterior = $ultimoHistorico ? $ultimoHistorico->estado_nuevo : 'pendiente';

            // Verificar que estado_anterior no sea NULL
            if (!$estadoAnterior) {
                $estadoAnterior = 'recibido';
            }

            // Crear un nuevo registro en `historicos`
            $historico = HistorialDocumento::create([
                'id_documento' => $idDocumento, // ID del documento asociado
                'id_usuario' => request()->user()->id, // Usuario que realiza la asignación
                'destinatario' => $request->destinatario_id, // Usuario destinatario
                'estado_anterior' => $estadoAnterior, // Estado anterior o predeterminado
                'estado_nuevo' => $request->estado_nuevo, // Estado nuevo
                'fecha_cambio' => now(), // Fecha del cambio
                'observaciones' => $request->observaciones, // Observaciones opcionales
                'origen' => $origen, // Especificamos el origen como "emitido"

            ]);

            // Obtener el destinatario
            $destinatario = User::find($request->destinatario_id);

            // Enviar la notificación al destinatario
            if ($destinatario) {
                $destinatario->notify(new DocumentoRecibidoAsignado($historico));
            }

            return redirect()->route('documentos_recibidos.index')->with('success', 'Asignación registrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.documentos_recibidos')->with('error', 'Error al asignar destinatario: ' . $e->getMessage());
        }
    }
    public function asignarEmitidos(Request $request, $idDocumento)
    {
        $request->validate([
            'destinatario' => 'nullable|exists:users,id',
            'estado_nuevo' => 'required|string|in:por firma,observado,en proceso,otro',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            // Verificar que el documento existe
            $documento = DocumentoEmitido::findOrFail($idDocumento);
            //$documento = DocumentoEmitido::where('numero_oficio', $idDocumento)->firstOrFail();


            // Obtener el último estado del histórico (si existe)
            $ultimoHistorico = HistorialDocumento::where('id_documento', $idDocumento)->latest('fecha_cambio')->first();
            $estadoAnterior = $ultimoHistorico ? $ultimoHistorico->estado_nuevo : 'pendiente';

            // Verificar que estado_anterior no sea NULL
            if (!$estadoAnterior) {
                $estadoAnterior = 'recibido';
            }
            $origen = 'emitido'; // Puedes cambiar esto según la lógica que desees


            // Crear un nuevo registro en `historicos`
            $historico = HistorialDocumento::create([
                'id_documento' => $idDocumento,//$request->numero_oficio, // ID del documento asociado
                'id_usuario' => request()->user()->id, // Usuario que realiza la asignación
                'destinatario' => $request->destinatario_id, // Usuario destinatario
                'estado_anterior' => $estadoAnterior, // Estado anterior o predeterminado
                'estado_nuevo' => $request->estado_nuevo, // Estado nuevo
                'fecha_cambio' => now(), // Fecha del cambio
                'observaciones' => $request->observaciones, // Observaciones opcionales
                'origen' => $origen, // Especificamos el origen como "emitido"

            ]);

            // Obtener el destinatario
            $destinatario = User::find($request->destinatario_id);

            // Enviar la notificación al destinatario
            if ($destinatario) {
                $destinatario->notify(new DocumentoRecibidoAsignado($historico));
            }

            return redirect()->route('documentos_emitidos.index')->with('success', 'Asignación registrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->with('error', 'Error al asignar destinatario: ' . $e->getMessage());
        }
    }


}