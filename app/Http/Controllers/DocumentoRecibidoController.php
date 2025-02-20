<?php

namespace App\Http\Controllers;

use App\Models\DocumentoRecibido;
use App\Models\Entidad;
use App\Models\HistorialDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentoEmitido;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;




class DocumentoRecibidoController extends Controller
{
    // Mostrar los documentos recibidos

    public function index(Request $request)
    {
        $user = request()->user();  // Obtener el usuario actual
        $entidades = Entidad::all();  // Obtener todas las entidades para la vista


        // Construir la consulta base
        $query = DocumentoRecibido::with([
            'HistorialDocumento' => function ($query) {
                $query->where('origen', 'recibido')  // Filtrar solo los documentos con origen "recibido"
                    ->orderBy('fecha_cambio', 'desc');
            },
        ]);


        $query->orderBy('fecha_recibido', 'desc');


        // Verificar si el usuario tiene el rol de "administrativo"
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

        // Filtrar por fecha de recepción
        if ($request->has('fecha_recibido') && $request->fecha_recibido != '') {
            $query->whereDate('fecha_recibido', $request->fecha_recibido);
        }

        // Filtrar por remitente
        if ($request->has('remitente') && $request->remitente != '') {
            $query->where('remitente', 'like', '%' . $request->remitente . '%');
        }

        // Filtrar por estado nuevo
        if ($request->has('estado_nuevo') && $request->estado_nuevo != '') {
            $query->whereHas('HistorialDocumento', function ($query) use ($request) {
                $query->where('origen', 'recibido')
                    ->where('estado_nuevo', $request->estado_nuevo);
            });
        }


        // Aplicar filtro de 'eliminado' basado en el rol del usuario
        if ($user->hasRole('Administrador') || $user->hasRole('Jefa DSA') || $user->hasRole('Secretaria')) {
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
        return view('documentos.documentos_recibidos', compact('users', 'documentos', 'entidades', 'historialDocumento', 'documentosRecibidos', 'numeroOficio'));
    }
    //probndo index


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


    // Mostrar formulario para crear un nuevo documento recibido
    public function create()
    {
        $entidades = Entidad::where('eliminado', false)->get(); // Trae las entidades activas
        $fecha_actual = now()->toDateString(); // Obtiene la fecha actual en formato YYYY-MM-DD
        return view('documentos_recibidos.create', compact('entidades', 'fecha_actual'));
    }

    // Guardar un nuevo documento recibido
    public function store(Request $request)
    {
        // Validación con mensajes personalizados
        $request->validate(
            [
                'numero_oficio' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('documentos_recibidos')->where(function ($query) use ($request) {
                        return $query->where('entidad_id', $request->entidad_id);
                    })
                ],
                //'numero_oficio' => 'required|string|max:50|unique:documentos_recibidos,numero_oficio,NULL,id,entidad_id,' . $request->entidad_id, // Combinación única de numero_oficio y entidad_id
                'asunto' => 'required|string|max:255',
                'fecha_recibido' => 'required|date',
                'tipo' => 'required',
                'remitente' => 'required|string|max:100',
                'entidad_id' => 'required|exists:entidades,id',
                'observaciones' => 'nullable|string|max:200',
                'formato_documento' => 'nullable|in:virtual,fisico', // Validación para el formato de documento
                'respondido_con' => 'nullable|exists:documentos_emitidos,numero_oficio', // Validación para relacionar con un documento emitido
            ],
            [
                'numero_oficio.required' => 'El campo Número de Oficio es obligatorio.',
                'numero_oficio.max' => 'El Número de Oficio no puede superar los 50 caracteres.',
                'numero_oficio.unique' => 'El Número de Oficio ya está registrado para esta entidad.',
                'asunto.required' => 'El campo Asunto es obligatorio.',
                'asunto.max' => 'El Asunto no puede superar los 255 caracteres.',
                'fecha_recibido.required' => 'El campo Fecha Recibido es obligatorio.',
                'fecha_recibido.date' => 'El campo Fecha Recibido debe ser una fecha válida.',
                'tipo.required' => 'El campo Tipo es obligatorio.',
                'tipo.in' => 'El campo Tipo debe ser uno de los siguientes valores: oficio, solicitud, otro.',
                'remitente.required' => 'El campo Remitente es obligatorio.',
                'remitente.max' => 'El campo Remitente no puede superar los 100 caracteres.',
                'entidad_id.required' => 'Debe seleccionar una Entidad remitente.',
                'entidad_id.exists' => 'La Entidad seleccionada no existe.',
                'observaciones.max' => 'El campo Observaciones no puede superar los 200 caracteres.',
                'formato_documento.in' => 'El formato de documento debe ser uno de los siguientes: virtual, fisico.',
                'respondido_con.exists' => 'El documento al que responde no existe.',
            ]
        );

        try {

            $numero_oficio = $request->numero_oficio ?: 'S/N(' . strtoupper(Str::random(2)) . ')';

            // Verificar si el checkbox de formato_documento está marcado (presente en la solicitud)
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Concatenación para el campo nombre
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $entidad = \App\Models\Entidad::find($request->entidad_id);
            $entidadSuperior = $entidad->entidad_superior_id ? \App\Models\Entidad::find($entidad->entidad_superior_id)->siglas : '';

            $nombre = $request->tipo . '-' . $numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-' . $entidad->siglas . ($entidadSuperior ? '-' . $entidadSuperior : '');

            // Crear el documento recibido
            $documento = DocumentoRecibido::create([
                'numero_oficio' => $numero_oficio,
                'fecha_recibido' => $request->fecha_recibido,
                'remitente' => $request->remitente,
                'asunto' => $request->asunto,
                'tipo' => $request->tipo,
                'observaciones' => $request->observaciones,
                'entidad_id' => $request->entidad_id,
                'eliminado' => false, // Guardamos como no eliminado
                'formato_documento' => $formatoDocumento, // Nuevo campo para tipo de documento
                'respondido_con' => $request->respondido_con, // Nuevo campo para el documento respondido
                'nombre_doc' => $nombre, // Campo nombre generado
            ]);

            // Crear un registro en la tabla 'historicos'
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';
                $origen = 'recibido'; // Puedes cambiar esto según la lógica que desees

                \App\Models\HistorialDocumento::create([
                    'id_documento' => $documento->id, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'estado_anterior' => 'Recibido', // Valor predeterminado
                    'estado_nuevo' => null, // Estado vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $request->observaciones ?? 'Documento Recibido registrado por ' . request()->user()->nombre . ' ' . request()->user()->apellido, // Observaciones del documento
                    'origen' => $origen, // Origen del cambio
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos_recibidos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico: ' . $e->getMessage());
            }

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento guardado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejo de errores de base de datos (violación de unicidad, etc.)
            if ($e->getCode() == 23000) { // Código de error para violación de restricciones de unicidad
                return redirect()->back()->withErrors([
                    'numero_oficio' => 'La combinación de Número de Oficio y Entidad ya existe. Por favor, verifique los datos ingresados.'
                ])->withInput();
            }

            // Otros errores
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error inesperado al guardar el documento. Por favor, intente nuevamente.'
            ])->withInput();
        }
    }


    public function storeFunca(Request $request)
    {
        // Validación con mensajes personalizados
        $request->validate([
            'numero_oficio' => 'required|string|max:50|unique:documentos_recibidos,numero_oficio', // Unicidad solo en documentos recibidos
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro',
            'remitente' => 'required|string|max:100',
            'entidad_id' => 'required|exists:entidades,id',
            'observaciones' => 'nullable|string|max:200',
        ], [
            'numero_oficio.required' => 'El campo Número de Oficio es obligatorio.',
            'numero_oficio.max' => 'El Número de Oficio no puede superar los 50 caracteres.',
            'numero_oficio.unique' => 'El Número de Oficio ya está registrado para esta entidad.',
            'asunto.required' => 'El campo Asunto es obligatorio.',
            'asunto.max' => 'El Asunto no puede superar los 255 caracteres.',
            'fecha_recibido.required' => 'El campo Fecha Recibido es obligatorio.',
            'fecha_recibido.date' => 'El campo Fecha Recibido debe ser una fecha válida.',
            'tipo.required' => 'El campo Tipo es obligatorio.',
            'tipo.in' => 'El campo Tipo debe ser uno de los siguientes valores: oficio, solicitud, otro.',
            'remitente.required' => 'El campo Remitente es obligatorio.',
            'remitente.max' => 'El campo Remitente no puede superar los 100 caracteres.',
            'entidad_id.required' => 'Debe seleccionar una Entidad remitente.',
            'entidad_id.exists' => 'La Entidad seleccionada no existe.',
            'observaciones.max' => 'El campo Observaciones no puede superar los 200 caracteres.',
        ]);

        try {
            // Crear el documento recibido
            $documento = DocumentoRecibido::create([
                'numero_oficio' => $request->numero_oficio,
                'fecha_recibido' => $request->fecha_recibido,
                'remitente' => $request->remitente,
                'asunto' => $request->asunto,
                'tipo' => $request->tipo,
                'observaciones' => $request->observaciones,
                'entidad_id' => $request->entidad_id,
                'eliminado' => false, // Guardamos como no eliminado
            ]);

            // Crear un registro en la tabla 'historicos'
            try {
                $userId = request()->user()->id;
                $observaciones = $request->observaciones ?? 'Registro inicial del documento';
                $origen = 'recibido'; // Puedes cambiar esto según la lógica que desees


                \App\Models\HistorialDocumento::create([
                    'id_documento' => $documento->id, // ID del documento recién creado
                    'id_usuario' => $userId, // Usuario autenticado
                    'estado_anterior' => 'Recibido', // Valor predeterminado
                    'estado_nuevo' => null, // Estado vacío para el registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual del sistema
                    'observaciones' => $observaciones, // Observaciones del documento
                    'origen' => $origen, // Origen del cambio
                ]);
            } catch (\Exception $e) {
                // Manejo de errores específicos al guardar en Historico
                return redirect()->route('documentos_recibidos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el histórico: ' . $e->getMessage());
            }

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento guardado correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejo de errores de base de datos (violación de unicidad, etc.)
            if ($e->getCode() == 23000) { // Código de error para violación de restricciones de unicidad
                return redirect()->back()->withErrors([
                    'numero_oficio' => 'La combinación de Número de Oficio y Entidad ya existe. Por favor, verifique los datos ingresados.'
                ])->withInput();
            }

            // Otros errores
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error inesperado al guardar el documento. Por favor, intente nuevamente.'
            ])->withInput();
        }
    }

    //Guardar respuesta
    public function storeRespuestaRecibida(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'numero_oficio' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('documentos_recibidos')->where(function ($query) use ($request) {
                    return $query->where('entidad_id', $request->entidad_id);
                })
            ],
            //'numero_oficio' => 'required|string|max:50|unique:documentos_emitidos,numero_oficio',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro',
            'remitente' => 'required|string|max:255',
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
            'remitente.required' => 'El campo remitente es obligatorio.',
            'entidad_id.required' => 'Debe seleccionar una entidad receptora válida.',
            'entidad_id.exists' => 'La entidad seleccionada no existe.',
            'observaciones.max' => 'El campo Observaciones no puede superar los 500 caracteres.',
            'respuesta_a.exists' => 'El documento de Respuesta seleccionado no existe.',
            //'formato_documento.required' => 'El formato del documento es obligatorio.',
            'formato_documento.in' => 'El formato del documento debe ser "virtual" o "fisico".',

        ]);

        try {

            $numero_oficio = $request->numero_oficio ?: 'S/N(' . strtoupper(Str::random(2)) . ')';

            // Determinar el formato_documento (virtual o fisico)
            $formatoDocumento = $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Generar el campo nombre_doc
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $entidad = \App\Models\Entidad::find($request->entidad_id);
            $entidadSuperior = $entidad->entidad_superior_id ? \App\Models\Entidad::find($entidad->entidad_superior_id)->siglas : '';

            //$nombreDoc = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-DSA/VRACAD';
            $nombreDoc = $request->tipo . '-' . $numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-' . $entidad->siglas . ($entidadSuperior ? '-' . $entidadSuperior : '');

            // Crear el documento emitido
            $documento = DocumentoRecibido::create([
                'numero_oficio' => $numero_oficio, // El número de oficio se toma desde el formulario
                'asunto' => $request->asunto,
                'fecha_recibido' => $request->fecha_recibido,
                'tipo' => $request->tipo,
                'remitente' => $request->remitente,
                'entidad_id' => $request->entidad_id,
                'observaciones' => $request->observaciones,
                'respuesta_a' => $request->documento_id, // El ID del documento de respuesta
                'formato_documento' => $formatoDocumento,
                'nombre_doc' => $nombreDoc,
                'eliminado' => false, // El documento no estará eliminado por defecto
            ]);

            // Registrar en HistorialDocumento
            $origen = 'recibido'; // Origen del primer registro
            $doc_name = $request->documento_nombre;
            try {
                // Primer registro en el historial
                \App\Models\HistorialDocumento::create([
                    'id_documento' => $documento->id, // ID del documento recién creado
                    'id_usuario' => $request->user()->id, // Usuario autenticado
                    'estado_anterior' => 'Creado', // Estado inicial
                    'estado_nuevo' => null, // Estado vacío para registro inicial
                    'fecha_cambio' => now(), // Fecha y hora actual
                    'observaciones' => $request->observaciones ?? 'Documento recibido registrado en respuesta a ' . $doc_name . ' por ' . request()->user()->nombre . ' ' . request()->user()->apellido, // Observaciones
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
                    'origen' => 'emitido', // Cambiar el origen a "recibido"
                ]);
            } catch (\Exception $e) {
                return redirect()->route('documentos_emitidos.index')->with('error', 'Documento guardado, pero ocurrió un error al registrar el historial: ' . $e->getMessage());
            }

            // Redireccionar con éxito
            return redirect()->route('documentos_emitidos.index')->with('success', 'Documento emitido creado correctamente.');
        } catch (\Exception $e) {
            // Manejo de errores
            return redirect()->back()->withErrors([
                'error' => 'Ocurrió un error al guardar el documento: ' . $e->getMessage(),
            ])->withInput();
        }
    }


    // Mostrar formulario para editar un documento recibido
    public function edit($id)
    {
        $documento = DocumentoRecibido::findOrFail($id);
        $entidades = Entidad::where('eliminado', false)->get(); // Trae las entidades activas
        return view('documentos.edit', compact('documento', 'entidades'));
    }

    // Actualizar un documento recibido    // Actualizar un documento recibido
    public function updatefunca(Request $request, $id)
    {
        $documento = DocumentoRecibido::findOrFail($id);
        $origen = 'recibido'; // Origen cambiado a "recibido"

        $validatedData = $request->validate([
            'numero_oficio' => 'required|string|max:50',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required|in:oficio,solicitud,otro',
            'remitente' => 'required|string|max:100',
            'entidad_id' => 'required|exists:entidades,id',
            'observaciones' => 'nullable|string|max:200',
        ]);

        // Validar unicidad de combinación (número de oficio y entidad_id)
        $existingDocumento = DocumentoRecibido::where('numero_oficio', $request->numero_oficio)
            ->where('entidad_id', $request->entidad_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingDocumento) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Ya existe un documento con el mismo número de oficio para la entidad seleccionada.',
            ]);
        }

        try {
            // Guardar el estado actual antes de la actualización para registrar el historial
            $estadoAnterior = $documento->estado ?? 'No definido'; // Si no hay un estado anterior definido, poner uno predeterminado

            // Actualizar el documento
            $documento->update($validatedData);

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento actualizado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => $estadoAnterior, // Estado anterior antes de la actualización
                'estado_nuevo' => $documento->estado ?? 'No definido', // Nuevo estado después de la actualización (si lo hay)
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => $request->observaciones ?? 'Documento recibido actualizado por ' . request()->user()->nombre . '' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "recibido"
            ]);

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $documento = DocumentoRecibido::findOrFail($id);
        $origen = 'recibido'; // Origen cambiado a "recibido"

        $validatedData = $request->validate([
            'numero_oficio' => 'required|string|max:50',
            'asunto' => 'required|string|max:255',
            'fecha_recibido' => 'required|date',
            'tipo' => 'required',
            'remitente' => 'required|string|max:100',
            'entidad_id' => 'required|exists:entidades,id',
            'observaciones' => 'nullable|string|max:200',
            'formato_documento' => 'nullable|in:virtual,fisico', // Validación del nuevo campo
        ]);

        // Validar unicidad de combinación (número de oficio y entidad_id)
        $existingDocumento = DocumentoRecibido::where('numero_oficio', $request->numero_oficio)
            ->where('entidad_id', $request->entidad_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingDocumento) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Ya existe un documento con el mismo número de oficio para la entidad seleccionada.',
            ]);
        }

        try {
            // Determinar el formato del documento
            $formatoDocumento = $request->has('formato_documento') && $request->formato_documento === 'virtual' ? 'virtual' : 'fisico';

            // Calcular el nuevo valor de `nombre_doc`
            $fechaRecibido = \Carbon\Carbon::parse($request->fecha_recibido)->format('Y');
            $entidad = \App\Models\Entidad::find($request->entidad_id);
            $entidadSuperior = $entidad->entidad_superior_id ? \App\Models\Entidad::find($entidad->entidad_superior_id)->siglas : '';

            $nombre = $request->tipo . '-' . $request->numero_oficio . '-' . $fechaRecibido . ($formatoDocumento === 'virtual' ? '-V' : '') . '-' . $entidad->siglas . ($entidadSuperior ? '-' . $entidadSuperior : '');

            // Guardar el estado actual antes de la actualización para registrar el historial
            //$estadoAnterior = $documento->estado ?? 'No definido'; // Si no hay un estado anterior definido, poner uno predeterminado
// Obtener el estado anterior del historial de documentos
            $ultimoHistorial = \App\Models\HistorialDocumento::where('id_documento', $documento->id)
                ->orderBy('fecha_cambio', 'desc')
                ->first();

            //$estadoAnterior = $ultimoHistorial ? $ultimoHistorial->estado_nuevo : 'No definido';
            $estadoAnterior = $documento->estado ?? 'No definido';
            // Actualizar el documento con los nuevos datos
            $documento->update(array_merge($validatedData, [
                'formato_documento' => $formatoDocumento, // Actualización del formato del documento
                'nombre_doc' => $nombre, // Actualización del nombre generado
            ]));

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento actualizado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => $estadoAnterior, // Estado anterior antes de la actualización
                'estado_nuevo' => 'Actualizado',//documento->estado ?? 'No definido', // Nuevo estado después de la actualización (si lo hay)
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => $request->observaciones ?? 'Documento recibido actualizado por ' . request()->user()->nombre . ' ' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "recibido"
            ]);

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ]);
        }
    }


    // Eliminar un documento recibido (eliminación lógica)
    public function destroy($id)
    {
        $documento = DocumentoRecibido::findOrFail($id);
        $origen = 'recibido'; // Origen cambiado a "recibido"

        try {
            // Actualizar el estado del documento a eliminado
            $documento->update(['eliminado' => true]);

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento eliminado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => 'Observado', // Estado anterior
                'estado_nuevo' => 'Eliminado', // Nuevo estado tras la eliminación
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => 'Documento recibido eliminado por ' . request()->user()->nombre . '' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "recibido"
            ]);

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Error al eliminar el documento: ' . $e->getMessage(),
            ]);
        }
    }

    public function restore($id)
    {
        $documento = DocumentoRecibido::findOrFail($id);
        $origen = 'recibido'; // Origen cambiado a "recibido" según lo solicitado

        try {
            // Cambiar el valor de 'eliminado' a false para restaurar el documento
            $documento->eliminado = false;
            $documento->save();

            // Registrar el historial del documento
            \App\Models\HistorialDocumento::create([
                'id_documento' => $documento->id, // ID del documento restaurado
                'id_usuario' => request()->user()->id, // Usuario autenticado
                'estado_anterior' => 'Eliminado', // Estado anterior
                'estado_nuevo' => 'Restaurado', // Nuevo estado tras la restauración
                'fecha_cambio' => now(), // Fecha y hora actual
                'observaciones' => 'Documento recibido restaurado por ' . request()->user()->nombre . '' . request()->user()->apellido, // Observación del cambio
                'origen' => $origen, // Origen como "recibido"
            ]);

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento restaurado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Error al restaurar el documento: ' . $e->getMessage(),
            ]);
        }
    }

    // Eliminar un documento recibido por completo del sistema
    public function forceDelete($id)
    {
        try {
            // Buscar el documento por ID
            $documento = DocumentoRecibido::findOrFail($id);

            // Eliminar todas las entradas relacionadas en la tabla HistorialDocumento
            //HistorialDocumento::where('id_documento', $documento->id)->delete();
            HistorialDocumento::where('id_documento', $documento->id)
                ->where('origen', 'recibido')
                ->delete();
            // Eliminar el documento por completo
            $documento->delete();

            return redirect()->route('documentos_recibidos.index')->with('success', 'Documento recibido eliminado por completo del sistema.');
        } catch (\Exception $e) {
            return redirect()->route('documentos_recibidos.index')->withErrors([
                'error' => 'Error al eliminar el documento por completo: ' . $e->getMessage(),
            ]);
        }
    }
}