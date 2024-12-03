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
            'archivo' => 'nullable|mimes:pdf,docx|max:2048', // Validación del archivo
        ]);

        // Subir el archivo si existe
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('documentos', 'public');
        } else {
            $archivoPath = null;
        }

        Documento::create([
            'numero_oficio' => $request->numero_oficio,
            'fecha_recepcion' => $request->fecha_recepcion,
            'remitente' => $request->remitente,
            'tipo' => $request->tipo,
            'descripcion' => $request->descripcion,
            'observaciones' => $request->observaciones,
            'archivo' => $archivoPath,  // Guardamos la ruta del archivo
        ]);

        return redirect()->route('documentos.index');
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
            'archivo' => 'nullable|mimes:pdf,docx|max:2048', // Validación de archivo
        ]);

        $documento = Documento::findOrFail($id);

        // Si hay un archivo nuevo, subimos el archivo
        if ($request->hasFile('archivo')) {
            // Eliminar el archivo viejo, si existe
            if ($documento->archivo) {
                Storage::disk('public')->delete($documento->archivo);
            }
            $archivoPath = $request->file('archivo')->store('documentos', 'public');
        } else {
            $archivoPath = $documento->archivo;  // Si no se subió archivo, mantenemos el existente
        }

        $documento->update([
            'numero_oficio' => $request->numero_oficio,
            'fecha_recepcion' => $request->fecha_recepcion,
            'remitente' => $request->remitente,
            'tipo' => $request->tipo,
            'descripcion' => $request->descripcion,
            'observaciones' => $request->observaciones,
            'archivo' => $archivoPath, // Guardamos la ruta del archivo
        ]);

        return redirect()->route('documentos.index');
    }


    // Eliminar un documento
    public function destroy($id)
    {
        Documento::destroy($id);
        return redirect()->route('documentos.index');
    }

}