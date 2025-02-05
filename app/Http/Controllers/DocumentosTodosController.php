<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentoRecibido;
use App\Models\DocumentoEmitido;
use App\Models\HistorialDocumento;
use Carbon\Carbon;


class DocumentosTodosController extends Controller
{
    public function index2(Request $request)
    {
        $user = request()->user();  // Obtener el usuario actual

        // Obtener filtros del request
        $numero_oficio = $request->input('numero_oficio');
        $fecha_recibido = $request->input('fecha_recibido');
        $destino = $request->input('destino');

        // Consulta base para documentos emitidos
        $documentosEmitidos = DB::table('documentos_emitidos')->select([
            'id',
            'numero_oficio',
            'destino',
            'asunto',
            'fecha_recibido',
            'eliminado',
            DB::raw("'emitido' as tipo")
        ]);

        // Consulta base para documentos recibidos
        $documentosRecibidos = DB::table('documentos_recibidos')->select([
            'id',
            'numero_oficio',
            'remitente as destino',
            'asunto',
            'fecha_recibido',
            'eliminado',
            DB::raw("'recibido' as tipo")
        ]);



        // Aplicar filtros a las consultas
        if ($numero_oficio) {
            $documentosEmitidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
            $documentosRecibidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
        }

        if ($fecha_recibido) {
            $documentosEmitidos->whereDate('fecha_recibido', $fecha_recibido);
            $documentosRecibidos->whereDate('fecha_recibido', $fecha_recibido);
        }

        if ($destino) {
            $documentosEmitidos->where('destino', 'like', "%{$destino}%");
            $documentosRecibidos->where('remitente', 'like', "%{$destino}%");
        }

        // Combinar las consultas y paginar los resultados
        $documentos = $documentosEmitidos->union($documentosRecibidos)
            ->orderBy('fecha_recibido', 'desc')
            ->paginate(10);

        // Retornar la vista con los documentos
        return view('documentos.documentos_todos', compact('documentos'));
    }
    public function index1(Request $request)
    {
        $numero_oficio = $request->input('numero_oficio');
        $fecha_recibido = $request->input('fecha_recibido');
        $destino = $request->input('destino');

        // Consulta para documentos emitidos
        $documentosEmitidos = DB::table('documentos_emitidos')
            ->select([
                'id',
                'numero_oficio',
                'destino',
                'asunto',
                'fecha_recibido',
                'eliminado',
                DB::raw("'emitido' as tipo")
            ]);

        // Consulta para documentos recibidos
        $documentosRecibidos = DB::table('documentos_recibidos')
            ->select([
                'id',
                'numero_oficio',
                'remitente as destino',
                'asunto',
                'fecha_recibido',
                'eliminado',
                DB::raw("'recibido' as tipo")
            ]);

        // Aplicar filtros
        if ($numero_oficio) {
            $documentosEmitidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
            $documentosRecibidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
        }

        if ($fecha_recibido) {
            $documentosEmitidos->whereDate('fecha_recibido', $fecha_recibido);
            $documentosRecibidos->whereDate('fecha_recibido', $fecha_recibido);
        }

        if ($destino) {
            $documentosEmitidos->where('destino', 'like', "%{$destino}%");
            $documentosRecibidos->where('remitente', 'like', "%{$destino}%");
        }

        // Combinar consultas, ordenar y paginar
        $documentos = $documentosEmitidos
            ->union($documentosRecibidos)
            ->orderBy('fecha_recibido', 'desc')
            ->get();

        // Obtener el historial relacionado para cada documento
        foreach ($documentos as $documento) {
            $documento->historial = DB::table('historial_documentos')
                ->where('id', $documento->id)
                ->orderBy('fecha_cambio', 'desc')
                ->get();
        }

        // Paginar manualmente (ya que usamos `get` en lugar de `paginate`)
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginatedDocumentos = collect($documentos)->slice(($currentPage - 1) * $perPage, $perPage);
        $documentosPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedDocumentos,
            count($documentos),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Retornar la vista con los documentos y sus historiales
        return view('documentos.documentos_todos', ['documentos' => $documentosPaginated]);
    }

    public function index(Request $request)
    {
        $numero_oficio = $request->input('numero_oficio');
        $fecha_recibido = $request->input('fecha_recibido');
        $destino = $request->input('destino');
        $user = request()->user();  // Obtener el usuario actual
        // Consulta para documentos recibidos
        $documentosRecibidos = DocumentoRecibido::with([
            'historialDocumento' => function ($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'entidad'
        ])
            ->select([
                'id',
                'numero_oficio',
                'nombre_doc',
                'asunto',
                'fecha_recibido',
                'entidad_id',
                'eliminado',
                'remitente as destino',
                DB::raw("'recibido' as tipo")
            ]);

        // Consulta para documentos emitidos
        $documentosEmitidos = DocumentoEmitido::with([
            'historialDocumento' => function ($query) {
                $query->orderBy('fecha_cambio', 'desc');
            },
            'entidad'
        ])
            ->select([
                'id',
                'numero_oficio',
                'nombre_doc',
                'asunto',
                'fecha_recibido',
                'entidad_id',
                'eliminado',
                'destino',
                DB::raw("'emitido' as tipo")
            ]);


        // Aplicar filtros por rol del usuario
        if ($user->rol === 'Administrativo') {
            $documentosEmitidos->whereHas('historialDocumento', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });

            $documentosRecibidos->whereHas('historialDocumento', function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhere('destinatario', $user->id);
            });
        }

        // Aplicar filtros
        if ($numero_oficio) {
            $documentosEmitidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
            $documentosRecibidos->where('numero_oficio', 'like', "%{$numero_oficio}%");
        }

        if ($fecha_recibido) {
            $documentosEmitidos->whereDate('fecha_recibido', $fecha_recibido);
            $documentosRecibidos->whereDate('fecha_recibido', $fecha_recibido);
        }

        if ($destino) {
            $documentosEmitidos->where('destino', 'like', "%{$destino}%");
            $documentosRecibidos->where('remitente', 'like', "%{$destino}%");
        }

        // Combinar consultas, ordenar y paginar
        $documentos = $documentosEmitidos
            ->union($documentosRecibidos)
            ->orderBy('fecha_recibido', 'desc')
            ->get();

        // Paginar manualmente (ya que usamos `get` en lugar de `paginate`)
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginatedDocumentos = collect($documentos)->slice(($currentPage - 1) * $perPage, $perPage);
        $documentosPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedDocumentos,
            count($documentos),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Retornar la vista con los documentos y sus historiales
        return view('documentos.documentos_todos', ['documentos' => $documentosPaginated]);
    }

    public function contarDocumentos1(Request $request)
    {
        // Contar los documentos en la tabla 'documentos_emitidos'
        $documentosEmitidosCount = DocumentoEmitido::count();

        // Contar los documentos en la tabla 'documentos_recibidos'
        $documentosRecibidosCount = DocumentoRecibido::count();

        // Sumar los documentos emitidos y recibidos
        $totalDocumentos = $documentosEmitidosCount + $documentosRecibidosCount;

        // Contar todos los usuarios registrados
        $usuariosCount = \App\Models\User::count();

        // Retornar la vista dashboard.blade.php con los conteos
        return view('dashboard', [
            'documentos_emitidos' => $documentosEmitidosCount,
            'documentos_recibidos' => $documentosRecibidosCount,
            'total_documentos' => $totalDocumentos,
            'usuarios_count' => $usuariosCount
        ]);
    }

    public function contarDocumentos(Request $request)
    {
        // Contar los documentos en la tabla 'documentos_emitidos'
        $documentosEmitidosCount = DocumentoEmitido::count();

        // Contar los documentos en la tabla 'documentos_recibidos'
        $documentosRecibidosCount = DocumentoRecibido::count();

        // Sumar los documentos emitidos y recibidos
        $totalDocumentos = $documentosEmitidosCount + $documentosRecibidosCount;

        // Contar todos los usuarios registrados
        $usuariosCount = \App\Models\User::count();

        // Contar los registros en la tabla 'historial_documentos' donde el estado_nuevo es 'atendido'
        $atendidosCount = DB::table('historial_documentos')
            ->where('estado_nuevo', 'atendido')
            ->count();
        $doc_pendientes = $totalDocumentos - $atendidosCount;

        // Obtener la fecha de hace 7 días
        $fechaInicio = Carbon::now()->subDays(7);

        // Obtener los documentos emitidos de los últimos 7 días
        $documentosEmitidosPorDia = DocumentoEmitido::selectRaw('DATE(fecha_recibido) as fecha, COUNT(*) as cantidad')
            ->where('fecha_recibido', '>=', $fechaInicio)
            ->groupBy('fecha_recibido')
            ->orderBy('fecha_recibido')
            ->get();

        // Obtener los documentos recibidos de los últimos 7 días
        $documentosRecibidosPorDia = DocumentoRecibido::selectRaw('DATE(fecha_recibido) as fecha, COUNT(*) as cantidad')
            ->where('fecha_recibido', '>=', $fechaInicio)
            ->groupBy('fecha_recibido')
            ->orderBy('fecha_recibido')
            ->get();

        // Generar un arreglo con las fechas de los últimos 7 días
        $diasUltimos7Dias = collect();
        $documentosEmitidosData = [];
        $documentosRecibidosData = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->toDateString();  // Fecha del día

            // Verificar si hay documentos emitidos o recibidos para este día
            $documentosEmitidosData[$fecha] = $documentosEmitidosPorDia->where('fecha', $fecha)->first()->cantidad ?? 0;
            $documentosRecibidosData[$fecha] = $documentosRecibidosPorDia->where('fecha', $fecha)->first()->cantidad ?? 0;

            // Agregar la fecha al arreglo de días
            $diasUltimos7Dias->push($fecha);
        }

        // Obtener los últimos 5 documentos emitidos
        $documentosEmitidos = DocumentoEmitido::latest()
            ->take(5)
            ->get();

        // Obtener los últimos 5 documentos recibidos
        $documentosRecibidos = DocumentoRecibido::latest()
            ->take(5)
            ->get();

        // Obtener el estado más reciente para los documentos emitidos
        foreach ($documentosEmitidos as $documento) {
            $estado = HistorialDocumento::where('id_documento', $documento->id)
                ->latest()
                ->first();

            // Si el estado nuevo es null, tomar el estado anterior
            $documento->estado = $estado ? ($estado->estado_nuevo ?? $estado->estado_anterior) : 'No disponible';
        }

        // Obtener el estado más reciente para los documentos recibidos
        foreach ($documentosRecibidos as $documento) {
            $estado = HistorialDocumento::where('id_documento', $documento->id)
                ->latest()
                ->first();

            // Si el estado nuevo es null, tomar el estado anterior
            $documento->estado = $estado ? ($estado->estado_nuevo ?? $estado->estado_anterior) : 'No disponible';
        }

        // Retornar la vista dashboard.blade.php con los conteos
        return view('dashboard', [
            'documentos_emitidos' => $documentosEmitidosCount,
            'documentos_recibidos' => $documentosRecibidosCount,
            'total_documentos' => $totalDocumentos,
            'usuarios_count' => $usuariosCount,
            'atendidos_count' => $atendidosCount,
            'pendientes_count' => $doc_pendientes,
            'documentosEmitidosData' => $documentosEmitidosData,
            'documentosRecibidosData' => $documentosRecibidosData,
            'diasUltimos7Dias' => $diasUltimos7Dias,
            'documentosEmitidos' => $documentosEmitidos,
            'documentosRecibidos' => $documentosRecibidos,
        ]);
    }



}