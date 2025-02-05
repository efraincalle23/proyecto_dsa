<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\User;
use App\Models\Historico;
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
        // Si el usuario es administrativo, filtrar los documentos asignados a él
        $user = request()->user();

        $users = User::all();
        $ultimoNumero = Documento::where('origen', 'emitido')->latest('numero_oficio')->value('numero_oficio');
        $nuevoNumero = $ultimoNumero ? str_pad((int) $ultimoNumero + 1, 3, '0', STR_PAD_LEFT) : '001';


        $query = Documento::with([
            'historicos' => function ($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'documentoPadre',
            'subDocumentos',
        ]);

        // Verificar si el usuario tiene el rol de "administrativo"
        if ($user->rol === 'Administrativo') {
            // Filtrar los documentos según el rol del usuario
            $query->whereHas('historicos', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });
        } else {
            

        }
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
        $users = User::all();
        $historicos = Historico::all();
        return view('documentos.index', compact('documentos', 'users', 'historicos', 'nuevoNumero'));
    }


    // Mostrar el formulario para crear un nuevo documento
    public function create()
    {

        return view('documentos.create');
    }
    public function obtenerNuevoNumeroOficio()
    {
        // Obtener el último número de oficio de los documentos emitidos
        $ultimoNumero = Documento::where('origen', 'emitido')->latest('numero_oficio')->value('numero_oficio');
        $nuevoNumero = $ultimoNumero ? str_pad((int) $ultimoNumero + 1, 3, '0', STR_PAD_LEFT) : '001';

        return $nuevoNumero;
    }

    public function emitidos(Request $request)
    {
        /*$documentos = Documento::all();
        return view('documentos.index', compact('documentos'));*/
        // Si el usuario es administrativo, filtrar los documentos asignados a él
        $user = request()->user();
        $ultimoNumero = Documento::where('origen', 'emitido')->latest('numero_oficio')->value('numero_oficio');
        $nuevoNumero = $this->obtenerNuevoNumeroOficio();

        // Obtener los documentos para el documento padre, paginados
        $documentos = Documento::paginate(10); // Paginando con 10 registros por página

        $query = Documento::with([
            'historicos' => function ($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'documentoPadre',
            'subDocumentos',
        ])->where('Origen', 'Emitido');


        // Verificar si el usuario tiene el rol de "administrativo"
        if ($user->rol === 'Administrativo') {
            // Filtrar los documentos según el rol del usuario
            $query->whereHas('historicos', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });
        } else {

        }
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
        $users = User::all();
        $historicos = Historico::all();
        return view('documentos.emitidos', compact('documentos', 'users', 'historicos', 'nuevoNumero'));

    }
    public function recibidos(Request $request)
    {
        /*$documentos = Documento::all();
        return view('documentos.index', compact('documentos'));*/
        // Si el usuario es administrativo, filtrar los documentos asignados a él
        $user = request()->user();

        $ultimoNumero = Documento::where('origen', 'emitido')->latest('numero_oficio')->value('numero_oficio');
        $nuevoNumero = $this->obtenerNuevoNumeroOficio();

        // Obtener los documentos para el documento padre, paginados
        $documentos = Documento::paginate(10); // Paginando con 10 registros por página


        $query = Documento::with([
            'historicos' => function ($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'documentoPadre',
            'subDocumentos',
        ])->where('Origen', 'Recibido');

        // Verificar si el usuario tiene el rol de "administrativo"
        if ($user->rol === 'Administrativo') {
            // Filtrar los documentos según el rol del usuario
            $query->whereHas('historicos', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });
        } else {

        }
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
        $users = User::all();
        $historicos = Historico::all();
        return view('documentos.recibidos', compact('documentos', 'users', 'historicos', 'nuevoNumero'));
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
            'documento_padre_id' => 'nullable|exists:documentos,id_documento', // Validar documento padre
            'origen' => 'required|max:100', // Nueva validación para 'origen'
        ]);

        try {
            // Guardar archivo si existe
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            // Crear el documento con el campo origen agregado
            $documento = Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
                'documento_padre_id' => $request->documento_padre_id, // Actualizar documento padre
                'origen' => $request->origen, // Agregar el valor de origen
            ]);

            // Crear registro en historicos
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';

                \App\Models\Historico::create([
                    'id_documento' => $documento->id_documento, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'destinatario' => $request->destinatario, // Puede ser nulo
                    'estado_anterior' => 'Recibido', // Valor predeterminado según la migración
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
    public function storeRecibido(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20|unique:documentos,numero_oficio',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
            'documento_padre_id' => 'nullable|exists:documentos,id_documento', // Validar documento padre
            'origen' => '$request->origen', // Nueva validación para 'origen'
        ]);

        try {
            // Guardar archivo si existe
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            // Crear el documento con el campo origen agregado
            $documento = Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
                'documento_padre_id' => $request->documento_padre_id, // Actualizar documento padre
                'origen' => 'Recibido', // Agregar el valor de origen
            ]);

            // Crear registro en historicos
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';

                \App\Models\Historico::create([
                    'id_documento' => $documento->id_documento, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'destinatario' => $request->destinatario, // Puede ser nulo
                    'estado_anterior' => 'Recibido', // Valor predeterminado según la migración
                    'estado_nuevo' => null, // Campo vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $observaciones,
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos.emitidos')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico.' . $e->getMessage());
            }

            return redirect()->route('documentos.recibidos')->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.recibidos')->with('error', 'Error al guardar el documento.');
        }
    }
    public function storeEmitido(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20|unique:documentos,numero_oficio',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
            'documento_padre_id' => 'nullable|exists:documentos,id_documento', // Validar documento padre
            'origen' => '$request->origen', // Nueva validación para 'origen'
        ]);

        try {
            // Guardar archivo si existe
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            // Crear el documento con el campo origen agregado
            $documento = Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
                'documento_padre_id' => $request->documento_padre_id, // Actualizar documento padre
                'origen' => 'Emitido', // Agregar el valor de origen
            ]);

            // Crear registro en historicos
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';

                \App\Models\Historico::create([
                    'id_documento' => $documento->id_documento, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'destinatario' => $request->destinatario, // Puede ser nulo
                    'estado_anterior' => 'Recibido', // Valor predeterminado según la migración
                    'estado_nuevo' => null, // Campo vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $observaciones,
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos.emitidos')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico.' . $e->getMessage());
            }

            return redirect()->route('documentos.emitidos')->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.emitidos')->with('error', 'Error al guardar el documento.');
        }
    }


    public function storeRespuesta(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|max:20|unique:documentos,numero_oficio',
            'fecha_recepcion' => 'required|date',
            'remitente' => 'required|max:50',
            'tipo' => 'required|max:20',
            'descripcion' => 'required|max:200',
            'observaciones' => 'nullable|max:200',
            'archivo' => 'nullable|mimes:pdf,docx|max:2048',
            'documento_padre_id' => 'required|exists:documentos,id_documento', // Validar documento padre
            'origen' => '$request->origen', // Nueva validación para 'origen'
        ]);

        try {
            // Guardar archivo si existe
            $archivoPath = $request->hasFile('archivo') ? $request->file('archivo')->store('documentos', 'public') : null;

            // Crear el documento con el campo origen agregado
            $documento = Documento::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
                'documento_padre_id' => $request->documento_padre_id, // Actualizar documento padre
                'origen' => 'Emitido', // Agregar el valor de origen
            ]);

            // Crear registro en historicos
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';

                \App\Models\Historico::create([
                    'id_documento' => $documento->id_documento, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'destinatario' => $request->destinatario, // Puede ser nulo
                    'estado_anterior' => 'Recibido', // Valor predeterminado según la migración
                    'estado_nuevo' => null, // Campo vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $observaciones,
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos.emitidos')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico.' . $e->getMessage());
            }

            return redirect()->route('documentos.emitidos')->with('success', 'Documento guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos.emitidos')->with('error', 'Error al guardar el documento.');
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
            'documento_padre_id' => 'nullable|exists:documentos,id_documento', // Validar documento padre
            'origen' => 'nullable|max:100', // Nueva validación para 'origen'
        ]);

        try {
            $documento = Documento::findOrFail($id);

            if ($request->hasFile('archivo')) {
                // Eliminar archivo antiguo si existe
                if ($documento->archivo) {
                    Storage::disk('public')->delete($documento->archivo);
                }
                // Guardar el nuevo archivo
                $archivoPath = $request->file('archivo')->store('documentos', 'public');
            } else {
                $archivoPath = $documento->archivo;
            }

            // Actualizar documento, incluyendo el campo 'origen'
            $documento->update([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recepcion' => $request->fecha_recepcion,
                'remitente' => $request->remitente,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'archivo' => $archivoPath,
                'documento_padre_id' => $request->documento_padre_id, // Actualizar documento padre
                'origen' => $request->origen, // Actualizar el campo origen
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