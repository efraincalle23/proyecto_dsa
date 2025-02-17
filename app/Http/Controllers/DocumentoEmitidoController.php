<?php

namespace App\Http\Controllers;

use App\Models\DocumentoEmitido;
use App\Models\Entidad;
use App\Models\HistorialDocumento;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentoRecibido;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class DocumentoEmitidoController extends Controller
{
    // Mostrar los documentos emitidos
    public function index(Request $request)
    {
        $user = request()->user();  // Obtener el usuario actual
        $entidades = Entidad::all();  // Obtener todas las entidades para la vista

        $query = DocumentoEmitido::with([
            'HistorialDocumento' => function ($query) {
                $query->where('origen', 'emitido')  // Filtrar solo los documentos con origen "recibido"
                    ->orderBy('fecha_cambio', 'desc');
            },
        ]);

        $query->orderBy('fecha_recibido', 'desc');

        if ($user->rol === 'Administrativo') {
            // Filtrar los documentos según el rol del usuario
            $query->whereHas('HistorialDocumento', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });
        } else {

        }

        // Filtrar por número de oficio
        if ($request->has('numero_oficio') && $request->numero_oficio != '') {
            $query->where('numero_oficio', 'like', '%' . $request->numero_oficio . '%');
        }

        // Filtrar por fecha de emisión
        if ($request->has('fecha_recibido') && $request->fecha_recibido != '') {
            $query->whereDate('fecha_recibido', $request->fecha_recibido);
        }

        // Filtrar por asunto
        if ($request->has('asunto') && $request->asunto != '') {
            $query->where('asunto', 'like', '%' . $request->asunto . '%');
        }

        // Filtrar por remitente
        if ($request->has('remitente') && $request->remitente != '') {
            $query->where('destino', 'like', '%' . $request->remitente . '%');
        }

        // Filtrar por estado nuevo
        if ($request->has('estado_nuevo') && $request->estado_nuevo != '') {
            $query->whereHas('HistorialDocumento', function ($query) use ($request) {
                $query->where('origen', 'emitido')
                    ->where('estado_nuevo', $request->estado_nuevo);
            });
        }
        // Aplicar filtro de 'eliminado' basado en el rol del usuario
        if ($user->hasRole('Administrador') || $user->hasRole('Jefa DSA')) {
            // Mostrar todos los documentos, incluidos los eliminados
            // No se aplica filtro de 'eliminado'
        } /*else {
// Mostrar solo los documentos activos (no eliminados)
$query->where('eliminado', false);
}*/

        // Obtener los documentos con paginación
        $documentos = $query->paginate(10);  // Ajusta el número de resultados por página
        $historialDocumento = HistorialDocumento::all();
        $users = User::all();
        $documentosRecibidos = DocumentoRecibido::where('eliminado', false)->get();
        $numeroOficio = $this->generarNumeroOficio(); // Generar el siguiente número de oficio


        // Pasar los documentos y entidades a la vista
        return view('documentos.documentos_emitidos', compact('users', 'documentos', 'entidades', 'historialDocumento', 'documentosRecibidos', 'numeroOficio'));
    }
    private function generarNumeroOficioFuncionaba()
    {
        // Obtener el último número de oficio registrado
        $ultimoDocumento = DocumentoEmitido::orderBy('numero_oficio', 'desc')->first();

        // Si no hay registros, empezamos con 001
        if (!$ultimoDocumento) {
            return '001';
        }

        // Incrementar el último número de oficio
        $ultimoNumero = intval($ultimoDocumento->numero_oficio);
        $nuevoNumero = str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);

        return $nuevoNumero;
    }
    private function generarNumeroOficio()
    {
        // Obtener el número de inicio desde la tabla 'configuraciones'
        $inicio = DB::table('configuraciones')->where('clave', 'numero_oficio_inicio')->value('valor');

        // Obtener el último número de oficio registrado
        $ultimoDocumento = DocumentoEmitido::orderBy('numero_oficio', 'desc')->first();

        if (!$ultimoDocumento) {
            // Si no hay documentos previos, empezamos con el número de inicio
            return str_pad($inicio, 3, '0', STR_PAD_LEFT);
        }

        $ultimoNumero = intval($ultimoDocumento->numero_oficio);

        // Si el último número es menor que el número de inicio, empezamos desde ahí
        if ($ultimoNumero < $inicio) {
            return str_pad($inicio, 3, '0', STR_PAD_LEFT);
        }

        // De lo contrario, seguimos con la numeración normal
        return str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
    }


    // Mostrar formulario para crear un nuevo documento emitido
    public function create()
    {
        $numeroOficio = $this->generarNumeroOficio(); // Generar el siguiente número de oficio

        $entidades = Entidad::where('eliminado', false)->get(); // Trae las entidades activas
        $fecha_actual = now()->toDateString(); // Obtiene la fecha actual en formato YYYY-MM-DD
        return view('documentos.create', compact('entidades', 'fecha_actual', 'numeroOficio'));

        // Documentos no eliminados
    }

    // Guardar un nuevo documento emitido


    public function store10_02(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'numero_oficio' => [
                'required',
                'string',
                'max:50',
                Rule::unique('documentos_emitidos')->where(function ($query) use ($request) {
                    return $request->tipo !== 'oficio_circular';
                }),
            ],
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro,oficio_circular',
            'destinos' => 'required_if:tipo,oficio_circular|array|min:1', // Asegurar que haya al menos un destino seleccionado
            'destinos.*' => 'string', // Validar cada destino individualmente
            'entidad_id' => 'nullable|exists:entidades,id', // Se manejará manualmente si es oficio_circular
            'observaciones' => 'nullable|string|max:500',
            'Respuesta_A' => 'nullable|exists:documentos_recibidos,id',
            'formato_documento' => 'nullable|in:virtual,fisico',
        ], [
            'destinos.required_if' => 'Debe seleccionar al menos un destino si el tipo es Oficio Circular.',
        ]);

        try {
            // Verificar el formato de documento
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el nombre del documento
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            if ($request->tipo === 'oficio_circular') {
                // Convertir los destinos seleccionados en una cadena separada por comas
                $destinoTexto = implode(", ", $request->destinos);
                $entidadId = 11; // Asignar la entidad con ID 11

                // Crear un único registro con los destinos seleccionados
                $documento = DocumentoEmitido::create([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $destinoTexto, // Destinos concatenados
                    'entidad_id' => $entidadId,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => false,
                ]);

            } else {
                // Crear un único registro normal
                $documento = DocumentoEmitido::create([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $request->destino,
                    'entidad_id' => $request->entidad_id,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => false,
                ]);
            }

            // Registrar en HistorialDocumento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id,
                'id_usuario' => $request->user()->id,
                'estado_anterior' => 'Creado',
                'estado_nuevo' => null,
                'fecha_cambio' => now(),
                'observaciones' => $request->observaciones ?? 'Documento emitido registrado por ' . $request->user()->nombre . ' ' . $request->user()->apellido,
                'origen' => 'emitido',
            ]);

            // Redireccionar con éxito
            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error al guardar el documento: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function store(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'numero_oficio' => [
                'required',
                'string',
                'max:50',
                Rule::unique('documentos_emitidos')->where(function ($query) use ($request) {
                    return $request->tipo !== 'oficio_circular';
                }),
            ],
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro,oficio_circular',
            'destinos' => 'required_if:tipo,oficio_circular|array|min:1', // Asegurar que haya al menos un destino seleccionado
            'destinos.*' => 'string', // Validar cada destino individualmente
            'entidad_id' => 'nullable|exists:entidades,id', // Se manejará manualmente si es oficio_circular
            'observaciones' => 'nullable|string|max:500',
            'Respuesta_A' => 'nullable|exists:documentos_recibidos,id',
            'formato_documento' => 'nullable|in:virtual,fisico',
        ], [
            'destinos.required_if' => 'Debe seleccionar al menos un destino si el tipo es Oficio Circular.',
        ]);

        try {
            // Verificar el formato de documento
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el nombre del documento
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            if ($request->tipo === 'oficio_circular') {
                // Convertir los destinos seleccionados en una cadena separada por comas
                $destinoTexto = implode(", ", $request->destinos);

                if ($destinoTexto == "Decanatos") {
                    $entidadId = 132;
                } elseif ($destinoTexto == "Direcciones de Escuelas") {
                    $entidadId = 133;
                } elseif ($destinoTexto == "Departamentos Académicos") {
                    $entidadId = 134;
                } elseif ($destinoTexto == "Decanatos, Direcciones de Escuelas") {
                    $entidadId = 135;
                } elseif ($destinoTexto == "Decanatos, Departamentos Académicos") {
                    $entidadId = 136;
                } elseif ($destinoTexto == "Direcciones de Escuelas, Departamentos Académicos") {
                    $entidadId = 137;
                } elseif ($destinoTexto == "Decanatos, Direcciones de Escuelas, Departamentos Académicos") {
                    $entidadId = 138;
                }


                //$entidadId = 11; // Asignar la entidad con ID 11

                // Crear un único registro con los destinos seleccionados
                $documento = DocumentoEmitido::create([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $destinoTexto, // Destinos concatenados
                    'entidad_id' => $entidadId,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => false,
                ]);

            } else {
                // Crear un único registro normal
                $documento = DocumentoEmitido::create([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $request->destino,
                    'entidad_id' => $request->entidad_id,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => false,
                ]);
            }

            // Registrar en HistorialDocumento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id,
                'id_usuario' => $request->user()->id,
                'estado_anterior' => 'Creado',
                'estado_nuevo' => null,
                'fecha_cambio' => now(),
                'observaciones' => $request->observaciones ?? 'Documento emitido registrado por ' . $request->user()->nombre . ' ' . $request->user()->apellido,
                'origen' => 'emitido',
            ]);

            // Redireccionar con éxito
            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error al guardar el documento: ' . $e->getMessage(),
            ])->withInput();
        }
    }


    public function storeRespuesta1120(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'numero_oficio' => 'required|string|max:50|unique:documentos_emitidos,numero_oficio',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro',
            'destino' => 'required|string|max:255',
            'entidad_id' => 'required|exists:entidades,id',
            'observaciones' => 'nullable|string|max:500',
            'respuesta_a' => 'nullable|exists:documentos_recibidos,id', // Validamos que exista el documento de respuesta
            'formato_documento' => 'nullable|in:virtual,fisico', // Validación para el formato de documento
        ], [
            'numero_oficio.required' => 'El campo Número de Oficio es obligatorio.',
            'numero_oficio.unique' => 'El Número de Oficio ya está registrado.',
            'asunto.required' => 'El campo Asunto es obligatorio.',
            'fecha_recibido.required' => 'El campo Fecha de Emisión es obligatorio.',
            'fecha_recibido.date' => 'La Fecha de Emisión debe ser una fecha válida.',
            'tipo.required' => 'El campo Tipo es obligatorio.',
            'tipo.in' => 'El campo Tipo debe ser uno de los siguientes valores: oficio, solicitud, otro.',
            'destino.required' => 'El campo Destino es obligatorio.',
            'entidad_id.required' => 'Debe seleccionar una entidad receptora válida.',
            'entidad_id.exists' => 'La entidad seleccionada no existe.',
            'observaciones.max' => 'El campo Observaciones no puede superar los 500 caracteres.',
            'respuesta_a.exists' => 'El documento de Respuesta seleccionado no existe.',
            //'formato_documento.required' => 'El formato del documento es obligatorio.',
            'formato_documento.in' => 'El formato del documento debe ser "virtual" o "fisico".',

        ]);

        try {
            // Determinar el formato_documento (virtual o fisico)
            $formatoDocumento = $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el campo nombre_doc
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $entidad = \App\Models\Entidad::find($request->entidad_id);
            $entidadSuperior = $entidad->entidad_superior_id ? \App\Models\Entidad::find($entidad->entidad_superior_id)->siglas : '';

            $nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            // Crear el documento emitido
            $documento = DocumentoEmitido::create([
                'numero_oficio' => $request->numero_oficio, // El número de oficio se toma desde el formulario
                'asunto' => $request->asunto,
                'fecha_recibido' => $request->fecha_recibido,
                'tipo' => $request->tipo,
                'destino' => $request->destino,
                'entidad_id' => $request->entidad_id,
                'observaciones' => $request->observaciones,
                'respuesta_a' => $request->documento_id, // El ID del documento de respuesta
                'formato_documento' => $formatoDocumento,
                'nombre_doc' => $nombreDoc,
                'eliminado' => false, // El documento no estará eliminado por defecto
            ]);

            // Registrar en HistorialDocumento
            $origen = 'emitido'; // Origen del primer registro
            $doc_name = $request->documento_nombre;
            try {
                // Primer registro en el historial
                \App\Models\HistorialDocumento::create([
                    'id_documento' => $documento->id, // ID del documento recién creado
                    'id_usuario' => $request->user()->id, // Usuario autenticado
                    'estado_anterior' => 'Creado', // Estado inicial
                    'estado_nuevo' => null, // Estado vacío para registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual
                    'observaciones' => $request->observaciones ?? 'Documento emitido registrado en respuesta a ' . $doc_name . ' por ' . request()->user()->nombre . ' ' . request()->user()->apellido, // Observaciones
                    'origen' => $origen, // Especificamos el origen como "emitido"
                ]);

                // Segundo registro en el historial (como respuesta)
                \App\Models\HistorialDocumento::create([
                    'id_documento' => $request->documento_id, // El ID del documento original que se está respondiendo
                    'id_usuario' => $request->user()->id, // Usuario autenticado
                    'estado_anterior' => 'Creado', // Estado inicial
                    'estado_nuevo' => null, // Estado vacío para registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual
                    'observaciones' => '' . request()->user()->nombre . ' ' . request()->user()->apellido . ' ha generado el ' . $nombreDoc . ' como respuesta a este documento', // Observaciones con el ID del documento
                    'origen' => 'recibido', // Cambiar el origen a "recibido"
                ]);
            } catch (\Exception $e) {
                return redirect()->route('documentos_recibidos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el historial: ' . $e->getMessage());
            }

            // Redireccionar con éxito
            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento emitido creado correctamente.');
        } catch (\Exception $e) {
            // Manejo de errores
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error al guardar el documento: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function storeRespuesta(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'numero_oficio' => 'required|string|max:50|unique:documentos_emitidos,numero_oficio',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,oficio_circular,otro',
            'destinos' => 'required_if:tipo,oficio_circular|array|min:1', // Asegurar que haya al menos un destino seleccionado
            'destinos.*' => 'string', // Validar cada destino individualmente


            //'destinos' => 'required_if:tipo,oficio_circular|array',
            //'destino' => 'required_unless:tipo,oficio_circular|string|max:255',
            'entidad_id' => 'required_unless:tipo,oficio_circular|exists:entidades,id',
            'observaciones' => 'nullable|string|max:500',
            'respuesta_a' => 'nullable|exists:documentos_recibidos,id',
            'formato_documento' => 'nullable|in:virtual,fisico',
        ], [
            'tipo.in' => 'El campo Tipo debe ser uno de los siguientes valores: oficio, solicitud, oficio_circular, otro.',
            'destinos.required_if' => 'Debe seleccionar al menos un destino para los oficios circulares.',
        ]);

        try {
            // Determinar el formato del documento
            $formatoDocumento = $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el campo nombre_doc
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $nombreTipo = ($request->tipo === 'oficio_circular') ? 'Oficio_circular' : strtoupper($request->tipo);
            $nombreDoc = $nombreTipo . '-' . $request->numero_oficio . '-' . $fechaRecibido;
            $nombreDoc .= ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            // Manejo especial para oficio_circular
            if ($request->tipo === 'oficio_circular') {
                $destinoTexto = implode(", ", $request->destinos);

                $entidadId = match ($destinoTexto) {
                    "Decanatos" => 132,
                    "Direcciones de Escuelas" => 133,
                    "Departamentos Académicos" => 134,
                    "Decanatos, Direcciones de Escuelas" => 135,
                    "Decanatos, Departamentos Académicos" => 136,
                    "Direcciones de Escuelas, Departamentos Académicos" => 137,
                    "Decanatos, Direcciones de Escuelas, Departamentos Académicos" => 138,
                    default => null,
                };

                $destino = $destinoTexto;
            } else {
                $entidadId = $request->entidad_id;
                $destino = $request->destino;
            }

            // Crear el documento emitido
            $documento = DocumentoEmitido::create([
                'numero_oficio' => $request->numero_oficio,
                'asunto' => $request->asunto,
                'fecha_recibido' => $request->fecha_recibido,
                'tipo' => $request->tipo,
                'destino' => $destino,
                'entidad_id' => $entidadId,
                'observaciones' => $request->observaciones,
                'respuesta_a' => $request->respuesta_a,
                'formato_documento' => $formatoDocumento,
                'nombre_doc' => $nombreDoc,
                'eliminado' => false,
            ]);

            // Registrar en HistorialDocumento (dos veces)
            $usuario = $request->user();
            $observacionGeneral = $usuario->nombre . ' ' . $usuario->apellido . ' ha generado el ' . $nombreDoc;

            try {
                // Registro como documento emitido
                \App\Models\HistorialDocumento::create([
                    'id_documento' => $documento->id,
                    'id_usuario' => $usuario->id,
                    'estado_anterior' => 'Creado',
                    'estado_nuevo' => null,
                    'fecha_cambio' => now(),
                    'observaciones' => 'Documento emitido registrado como respuesta.',
                    'origen' => 'emitido',
                ]);

                // Registro como documento recibido (respuesta)
                if ($request->respuesta_a) {
                    \App\Models\HistorialDocumento::create([
                        'id_documento' => $request->respuesta_a,
                        'id_usuario' => $usuario->id,
                        'estado_anterior' => 'Creado',
                        'estado_nuevo' => null,
                        'fecha_cambio' => now(),
                        'observaciones' => $observacionGeneral . ' como respuesta a este documento.',
                        'origen' => 'recibido',
                    ]);
                }
            } catch (\Exception $e) {
                return redirect()->route('documentos_recibidos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el historial: ' . $e->getMessage());
            }

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento emitido creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error al guardar el documento: ' . $e->getMessage(),
            ])->withInput();
        }
    }


    // Mostrar formulario para editar un documento emitido
    public function edit($id)
    {
        $documento = DocumentoEmitido::findOrFail($id);
        $entidades = Entidad::where('eliminado', false)->get(); // Trae las entidades activas
        return view('documentos.edit', compact('documento', 'entidades'));
    }

    // Actualizar un documento emitido
    public function update11(Request $request, $id)
    {
        $documento = DocumentoEmitido::findOrFail($id);
        $origen = 'emitido'; // Origen cambiado a "emitido"

        // Validación de los campos
        $validatedData = $request->validate([
            'numero_oficio' => 'required|string|max:50',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro',
            'destino' => 'required|string|max:100',
            'entidad_id' => 'required|exists:entidades,id',
            'observaciones' => 'nullable|string|max:200',
            'Respuesta_A' => 'nullable|string|max:255',
            'eliminado' => 'nullable|boolean',
            'formato_documento' => 'nullable|in:virtual,fisico', // Validación para formato_documento
        ]);

        // Validar unicidad de combinación (número de oficio y entidad_id)
        $existingDocumento = DocumentoEmitido::where('numero_oficio', $request->numero_oficio)
            ->where('entidad_id', $request->entidad_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingDocumento) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Ya existe un documento con el mismo número de oficio para la entidad seleccionada.',
            ]);
        }

        try {
            // Determinar el formato_documento (virtual o fisico)
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el campo nombre_doc
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $entidad = \App\Models\Entidad::find($request->entidad_id);
            $entidadSuperior = $entidad->entidad_superior_id ? \App\Models\Entidad::find($entidad->entidad_superior_id)->siglas : '';

            $nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            // Guardar el estado actual antes de la actualización para registrar el historial
            $estadoAnterior = $documento->estado ?? 'No definido'; // Si no hay un estado anterior definido, poner uno predeterminado

            // Actualizar los campos del documento
            $documento->update([
                'numero_oficio' => $request->numero_oficio,
                'asunto' => $request->asunto,
                'fecha_recibido' => $request->fecha_recibido,
                'tipo' => $request->tipo,
                'destino' => $request->destino,
                'entidad_id' => $request->entidad_id,
                'observaciones' => $request->observaciones,
                'Respuesta_A' => $request->Respuesta_A,
                'formato_documento' => $formatoDocumento,
                'nombre_doc' => $nombreDoc,
                'eliminado' => $request->eliminado ?? false, // Mantener el estado de eliminado
            ]);

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento actualizado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => $estadoAnterior, // Estado anterior antes de la actualización
                'estado_nuevo' => $documento->estado ?? 'No definido', // Nuevo estado después de la actualización
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => $request->observaciones ?? 'Documento emitido actualizado por ' . request()->user()->nombre . ' ' . request()->user()->apellido,
                'origen' => $origen, // Origen como "emitido"
            ]);

            // Redireccionar con éxito
            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $documento = DocumentoEmitido::findOrFail($id);
        $origen = 'emitido';

        // Validación de los campos
        $validatedData = $request->validate([
            'numero_oficio' => 'required|string|max:50',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro,oficio_circular',
            'destinos' => 'required_if:tipo,oficio_circular|array|min:1',
            'destinos.*' => 'string',
            'destino' => 'nullable|string|max:100',
            'entidad_id' => 'nullable|exists:entidades,id',
            'observaciones' => 'nullable|string|max:200',
            'Respuesta_A' => 'nullable|string|max:255',
            'eliminado' => 'nullable|boolean',
            'formato_documento' => 'nullable|in:virtual,fisico',
        ], [
            'destinos.required_if' => 'Debe seleccionar al menos un destino si el tipo es Oficio Circular.',
        ]);
        // Validar unicidad de combinación (número de oficio y entidad_id)
        $existingDocumento = DocumentoEmitido::where('numero_oficio', $request->numero_oficio)
            ->where('entidad_id', $request->entidad_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingDocumento) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Ya existe un documento con el mismo número de oficio para la entidad seleccionada.',
            ]);
        }

        try {
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';

            if ($request->tipo === 'oficio_circular') {
                $destinoTexto = implode(", ", $request->destinos);

                $entidadId = match ($destinoTexto) {
                    "Decanatos" => 132,
                    "Direcciones de Escuelas" => 133,
                    "Departamentos Académicos" => 134,
                    "Decanatos, Direcciones de Escuelas" => 135,
                    "Decanatos, Departamentos Académicos" => 136,
                    "Direcciones de Escuelas, Departamentos Académicos" => 137,
                    "Decanatos, Direcciones de Escuelas, Departamentos Académicos" => 138,
                    default => null,
                };

                $documento->update([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $destinoTexto,
                    'entidad_id' => $entidadId,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => $request->eliminado ?? false,
                ]);
            } else {
                $documento->update([
                    'numero_oficio' => $request->numero_oficio,
                    'asunto' => $request->asunto,
                    'fecha_recibido' => $request->fecha_recibido,
                    'tipo' => $request->tipo,
                    'destino' => $request->destino,
                    'entidad_id' => $request->entidad_id,
                    'observaciones' => $request->observaciones,
                    'Respuesta_A' => $request->Respuesta_A,
                    'formato_documento' => $formatoDocumento,
                    'nombre_doc' => $nombreDoc,
                    'eliminado' => $request->eliminado ?? false,
                ]);
            }

            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id,
                'id_usuario' => request()->user()->id,
                'estado_anterior' => 'Actualizado',
                'estado_nuevo' => null,
                'fecha_cambio' => now(),
                'observaciones' => $request->observaciones ?? 'Documento emitido actualizado por ' . request()->user()->nombre . ' ' . request()->user()->apellido,
                'origen' => $origen,
            ]);

            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ]);
        }
    }


    // Eliminar un documento emitido (eliminación lógica)
    public function destroy($id)
    {
        $documento = DocumentoEmitido::findOrFail($id);
        $origen = 'emitido'; // Origen como "emitido" según lo solicitado

        try {
            // Actualizar el estado del documento a eliminado
            $documento->update(['eliminado' => true]);

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento eliminado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => 'Creado', // Estado anterior
                'estado_nuevo' => 'Eliminado', // Nuevo estado tras la eliminación
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => 'Documento emitido eliminado por ' . request()->user()->nombre . '' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "emitido"
            ]);

            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Error al eliminar el documento: ' . $e->getMessage(),
            ]);
        }
    }

    // Restaurar un documento emitido (restauración lógica)
    public function restore($id)
    {
        $documento = DocumentoEmitido::findOrFail($id);
        $origen = 'emitido'; // Origen como "emitido" según lo solicitado

        try {
            // Cambiar el valor de 'eliminado' a false
            $documento->eliminado = false;
            $documento->save();

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento restaurado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => 'Eliminado', // Estado anterior
                'estado_nuevo' => 'Restaurado', // Nuevo estado tras la restauración
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => 'Documento emitido restaurado por ' . request()->user()->nombre . '' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "emitido"
            ]);

            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido restaurado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Error al restaurar el documento: ' . $e->getMessage(),
            ]);
        }
    }

    // Eliminar un documento emitido por completo del sistema
    public function forceDelete($id)
    {
        try {
            // Buscar el documento por ID
            $documento = DocumentoEmitido::findOrFail($id);

            // Eliminar todas las entradas relacionadas en la tabla HistorialDocumento
            //HistorialDocumento::where('id_documento', $documento->id)->delete();
            HistorialDocumento::where('id_documento', $documento->id)
                ->where('origen', 'emitido')
                ->delete();
            // Eliminar el documento por completo
            $documento->delete();

            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido eliminado por completo del sistema.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_emitidos.index')->withErrors([
                'error' => 'Error al eliminar el documento por completo: ' . $e->getMessage(),
            ]);
        }
    }


}