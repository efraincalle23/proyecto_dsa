<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class DocumentoController extends AuthenticatedController
{
    //
    // Mostrar todos los documentos
    public function index(Request $request)
    {
        /*$documentos = Documento::all();
        return view('documentos.index', compact('documentos'));*/

        $query = Documento::query();  // No es necesario con 'with('rol')'

        if ($request->filled('numero_oficio')) {
            $query->where('numero_oficio', 'like', '%' . $request->numero_oficio . '%');
        }

        if ($request->filled('fecha_recepcion')) {
            $query->where('fecha_recepcion', 'like', '%' . $request->fecha_recepcion . '%');
        }

        if ($request->filled('remitente')) {
            $query->where('remitente', 'like', '%' . $request->remitente . '%');  // Filtrar por el campo 'rol'
        }
        if ($request->filled(key: 'tipo')) {
            $query->where('tipo', 'like', '%' . $request->tipo . '%');  // Filtrar por el campo 'rol'
        }

        $documentos = $query->paginate(10); // Pagina los resultados

        return view('documentos.index', compact('documentos'));
    }

    // Mostrar el formulario para crear un nuevo documento
    public function create()
    {
        return view('documentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20|unique:documentos,numero_oficio',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
        ]);

        try {
            // Guardar archivo si existe
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            // Crear el documento
            $documento = Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
            ]);

            // Crear registro en historicos
            try {
                // Crear registro en historicos
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';

                \App\Models\Historico::create([
                    'id_documento' => $documento->id_documento, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'destinatario' => $request->destinatario, // Puede ser nulo
                    'estado_anterior' => 'recibido', // Valor predeterminado según la migración
                    'estado_nuevo' => null, // Campo vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $observaciones,
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico.' . $e->getMessage());
            }

            return redirect()->route('documentos.index')->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->with('error', 'Error al guardar el documento.');
        }
    }

    // Almacenar un nuevo documento
    public function store2(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20|unique:documentos,numero_oficio',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
        ]);

        try {
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
            ]);

            return redirect()->route('documentos.index')->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->with('error', 'Error al guardar el documento.');
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
        ]);

        try {
            $documento = Documento::findOrFail($id);

            if ($request->hasFile('archivo')) {
                if ($documento->archivo) {
                    Storage::disk('public')->delete($documento->archivo);
                }
                $archivoPath = $request->file('archivo')->store('documentos', 'public');
            } else {
                $archivoPath = $documento->archivo;
            }

            $documento->update([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
            ]);

            return redirect()->route('documentos.index')->with('success', 'Documento modificado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->with('error', 'Error al modificar el documento.');
        }
    }



    // Eliminar un documento
    public function destroy($id)
    {
        try {
            $documento = Documento::findOrFail($id);

            if ($documento->archivo) {
                Storage::disk('public')->delete($documento->archivo);
            }

            $documento->delete();

            return redirect()->route('documentos.index')->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->with('error', 'Error al eliminar el documento.');
        }
    }



}