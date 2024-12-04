<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    //
    // Mostrar todos los documentos
    public function index()
    {
        $documentos = Documento::all();
        return view('documentos.index', compact('documentos'));
    }

    // Mostrar el formulario para crear un nuevo documento
    public function create()
    {
        return view('documentos.create');
    }
    // Almacenar un nuevo documento
    public function store(Request $request)
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