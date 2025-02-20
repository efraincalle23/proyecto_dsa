@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-white p-4 rounded shadow">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-bold text-dark">Documentos Emitidos</h3>

            <!-- Contenedor para los botones -->
            <div class="d-flex gap-2">
                <a href="{{ url('/exportar-emitidos') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
                </a>
                <!-- Botón para abrir el modal de creación -->
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createModal">
                    Nuevo Documento
                </button>
                <!-- Botón para abrir el modal -->
                @role('Administrador|Jefe DSA|Secretaria')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configModal">
                        <i class="bi bi-gear-fill"></i>
                    </button>
                @endrole

            </div>

            <!-- Modal -->
            <div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="configModalLabel">Actualizar Número de Inicio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('configuracion.actualizarNumeroOficioInicio') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="numero_oficio_inicio" class="form-label">Nuevo Número de Inicio:</label>
                                    <input type="number" class="form-control" name="numero_oficio_inicio"
                                        value="{{ old('numero_oficio_inicio') }}" min="1" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Mostrar mensajes de éxito o error global -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Resto del código de la vista... -->
        {{-- Filtros de búsqueda --}}
        <div>
            <form method="GET" action="{{ route('documentos_emitidos.index') }}" class="mb-4">
                <div class="row g-2">
                    <div class="col-12 col-sm-6 col-md-2">
                        <input type="text" name="numero_oficio" class="form-control"
                            placeholder="Buscar por número de oficio" value="{{ request('numero_oficio') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <input type="date" name="fecha_recibido" class="form-control"
                            placeholder="Buscar por fecha de recepción" value="{{ request('fecha_recibido') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="text" name="remitente" class="form-control" placeholder="Buscar por remitente"
                            value="{{ request('remitente') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="estado_nuevo" class="form-select">
                            <option value="">-- Seleccionar Estado --</option>
                            <option value="por firma" {{ request('estado_nuevo') == 'por firma' ? 'selected' : '' }}>Por
                                firma</option>
                            <option value="firmado" {{ request('estado_nuevo') == 'firmado' ? 'selected' : '' }}>Firmado
                            </option>
                            <option value="observado" {{ request('estado_nuevo') == 'observado' ? 'selected' : '' }}>
                                Observado</option>
                            <option value="por respuesta"
                                {{ request('estado_nuevo') == 'por respuesta' ? 'selected' : '' }}>Por respuesta</option>
                            <option value="para oficio" {{ request('estado_nuevo') == 'para oficio' ? 'selected' : '' }}>
                                Para oficio</option>
                            <option value="atendido" {{ request('estado_nuevo') == 'atendido' ? 'selected' : '' }}>
                                Atendido</option>
                            <option value="archivado" {{ request('estado_nuevo') == 'archivado' ? 'selected' : '' }}>
                                Archivado</option>

                            <option value="Eliminado" {{ request('estado_nuevo') == 'Eliminado' ? 'selected' : '' }}>
                                Eliminado
                            </option>
                            <option value="otro" {{ request('estado_nuevo') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                    </div>

                    <div class="col-12 col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('documentos_emitidos.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-sync-alt"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="table-responsive"> <!-- Agrega este contenedor -->
            <table class="table table-striped ">
                <thead>
                    <tr class="table-secondary">
                        <th>#</th>
                        <th>Número de Oficio</th>
                        <th>Dirigo a</th>
                        <th>Oficina</th>
                        <th>Asunto</th>
                        <th>Emitido</th>
                        <th>Atendido</th>
                        <th>Estado</th>
                        <th></th>
                        <th>Asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $documento)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($documento->nombre_doc) }}
                            </td>
                            <td>{{ $documento->destino }}</td>
                            <td>{{ $documento->entidad->siglas }}</td>
                            <td><!--Asunto-->
                                @php
                                    $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                @endphp

                                <!-- Historial del Documento -->
                                @if ($historicosDocumento->isEmpty())
                                    <p class="text-muted">No hay historial para este documento.</p>
                                @else
                                    @php
                                        // Ordenar los historiales por la fecha de registro en orden descendente (más reciente primero)
                                        $ultimoHistorial = $historicosDocumento->sortByDesc('fecha_registro')->first();
                                    @endphp
                                    <button type="button" class="btn btn-danger btn-sm popover-trigger"
                                        title="Asunto del Documento" data-bs-content="{{ $documento->asunto ?? 'S/A' }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                @endif
                            </td>
                            <td>{{ $documento->fecha_recibido }}</td>
                            <td>
                                <!--Atendido-->
                                @php
                                    $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                @endphp

                                <!-- Historial del Documento -->
                                @if ($historicosDocumento->isEmpty())
                                    <p class="text-muted">No hay historial para este documento.</p>
                                @else
                                    @php
                                        // Ordenar los historiales por la fecha de registro en orden descendente (más reciente primero)
                                        $ultimoHistorial = $historicosDocumento->sortByDesc('fecha_registro')->first();
                                    @endphp
                                    <!-- Mostrar el estado_nuevo del historial más reciente -->
                                    {{ $ultimoHistorial->usuario->nombre ?? 'N/A' }}
                                    {{ $ultimoHistorial->usuario->apellido ?? '' }}
                                @endif
                            </td>
                            <td>
                                <!--Estado-->
                                @php
                                    $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                @endphp

                                <!-- Historial del Documento -->
                                @if ($historicosDocumento->isEmpty())
                                    <p class="text-muted">No hay historial para este documento.</p>
                                @else
                                    @php
                                        // Ordenar los historiales por la fecha de registro en orden descendente (más reciente primero)
                                        $ultimoHistorial = $historicosDocumento->sortByDesc('fecha_registro')->first();
                                        $estado = $ultimoHistorial->estado_nuevo ?? $ultimoHistorial->estado_anterior;

                                        // Definir clases de Bootstrap según el estado con btn-outline
                                        $coloresEstados = [
                                            'por firma' => 'btn-outline-warning',
                                            'firmado' => 'btn-outline-success',
                                            'observado' => 'btn-outline-danger',
                                            'por respuesta' => 'btn-outline-primary',
                                            'para oficio' => 'btn-outline-info',
                                            'atendido' => 'btn-outline-success',
                                            'archivado' => 'btn-outline-secondary',
                                            'Eliminado' => 'btn-outline-dark',
                                            'Restaurado' => 'btn-outline-success',
                                            'Creado' => 'btn-outline-primary',
                                            'otro' => 'btn-outline-secondary',
                                        ];

                                        // Obtener la clase según el estado, si no existe, usar btn-outline-secondary
                                        $claseBoton = $coloresEstados[$estado] ?? 'btn-outline-secondary';
                                    @endphp

                                    <!-- Botón con color dinámico -->
                                    <button type="button" class="btn {{ $claseBoton }}"
                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                        {{ $estado }}
                                    </button>
                                @endif


                            </td>
                            <td>
                                <!--Estado ojito-->

                                @if ($documento->eliminado)
                                @else
                                    @role('Administrador|Jefe DSA|Secretaria')
                                        <button type="button" title="Haz clic para asignar" class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#estadoModal{{ $documento->id }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    @endrole
                                @endif
                                @php
                                    $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                @endphp

                                <!-- Historial del Documento -->
                                @if ($historicosDocumento->isEmpty())
                                    <p class="text-muted">No hay historial para este documento.</p>
                                @else
                                    @php
                                        // Ordenar los historiales por la fecha de registro en orden descendente (más reciente primero)
                                        $ultimoHistorial = $historicosDocumento->sortByDesc('fecha_registro')->first();
                                    @endphp

                                    @php
                                        $contenidoPopover = '';

                                        if ($ultimoHistorial->destinatarioUser) {
                                            $contenidoPopover =
                                                ($ultimoHistorial->usuario->nombre ?? 'N/A') .
                                                ' ' .
                                                ($ultimoHistorial->usuario->apellido ?? '') .
                                                ' derivó a ' .
                                                ($ultimoHistorial->destinatarioUser->nombre .
                                                    ' ' .
                                                    $ultimoHistorial->destinatarioUser->apellido);

                                            if ($ultimoHistorial->estado_nuevo) {
                                                $contenidoPopover .=
                                                    ' (' .
                                                    ($ultimoHistorial->estado_nuevo ?? 'S/A') .
                                                    ') - Observ: ' .
                                                    ($ultimoHistorial->observaciones ?? 'S/O');
                                            }
                                        } else {
                                            $contenidoPopover =
                                                ($ultimoHistorial->estado_nuevo ?? 'Sin asignar |') .
                                                ' ' .
                                                ($ultimoHistorial->estado_anterior ?? '') .
                                                ' por ' .
                                                ($ultimoHistorial->usuario->apellido ?? '') .
                                                ' ' .
                                                ($ultimoHistorial->usuario->nombre ?? '');
                                        }
                                    @endphp
                                    <button type="button" class="btn btn-success btn-sm popover-trigger"
                                        title="Ultimo estado" data-bs-content="{{ $contenidoPopover }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                @endif

                            </td>
                            <td>
                                @php
                                    $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                @endphp

                                <!-- Historial del Documento -->
                                @if ($historicosDocumento->isEmpty())
                                    <p class="text-muted">No hay historial para este documento.</p>
                                @else
                                    @php
                                        // Ordenar los historiales por la fecha de registro en orden descendente (más reciente primero)
                                        $ultimoHistorial = $historicosDocumento->sortByDesc('fecha_registro')->first();
                                    @endphp
                                    <!-- Mostrar el estado_nuevo del historial más reciente -->
                                    @if ($ultimoHistorial->destinatarioUser)
                                        {{ $ultimoHistorial->destinatarioUser->nombre }}
                                        {{ $ultimoHistorial->destinatarioUser->apellido }}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @role('Administrador|Jefe DSA')
                                @endrole
                                <!-- Botón para abrir el modal de edición -->
                                <button type="button" title="Haz clic para editar" class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#editModal-{{ $documento->id }}">
                                    <i class="bi bi-pencil-square"></i> <!-- Ícono de editar -->
                                </button>

                                @role('Administrador|Jefe DSA|Secretaria')
                                    <!-- Botón para abrir el modal de eliminación -->
                                    @if ($documento->eliminado)
                                    @else
                                        <button type="button" title="Haz clic para eliminar" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $documento->id }}">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    @endif
                                    <!-- Mostrar opción de restaurar si está eliminado -->
                                    @if ($documento->eliminado)
                                        <form action="{{ route('documentos_emitidos.restore', $documento->id) }} "
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" title="Haz clic para restaurar"
                                                class="btn btn-warning btn-sm"><i class="bi bi-arrow-repeat"></i></button>
                                        </form>
                                    @endif
                                @endrole

                                @if ($documento->eliminado)
                                @else
                                    <button type="button" title="Haz clic para derivar" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#asignarModal{{ $documento->id }}">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                @endif
                                <button type="button" title="Ver historial" class="btn btn-info btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#historialModal{{ $documento->id }}">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                <!--boton respuesta-->
                                @role('Administrador|Jefe DSA|Secretaria')
                                    <button type="button" title="Responder a este documento" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#respuestaModal-{{ $documento->id }}">
                                        <i class="bi bi-reply-all-fill"></i>
                                    </button>
                                @endrole
                                @role('Administrador|Jefe DSA')
                                    <!-- Botón que activa el modal -->
                                    <button type="button" title="Eliminar definitivamente" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal-{{ $documento->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Modal de confirmación -->
                                    <div class="modal fade" id="confirmDeleteModal-{{ $documento->id }}" tabindex="-1"
                                        aria-labelledby="confirmDeleteLabel-{{ $documento->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="confirmDeleteLabel-{{ $documento->id }}">
                                                        Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que deseas eliminar este documento por completo? <br>
                                                        <strong>Esta acción no se puede deshacer.</strong>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form
                                                        action="{{ route('documentos_emitidos.forceDelete', $documento->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endrole

                            </td>
                        </tr>
                        <!--Modal respuesta-->
                        <div class="modal fade" id="respuestaModal-{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="respuestaModal-{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form
                                        action="{{ route('documentos_recibidos.storeRespuestaRecibida', $documento->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="respuestaModalLabel-{{ $documento->id }}">
                                                Respuesta a
                                                {{ ucfirst($documento->nombre_doc) }} </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            @include('documentos.partials.formRespuestaReci', [
                                                'documento' => $documento,
                                            ])

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de edición -->
                        <div class="modal fade" id="editModal-{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="editModalLabel-{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('documentos_emitidos.update', $documento->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel-{{ $documento->id }}">Editar
                                                Documento
                                                {{ ucfirst($documento->nombre_doc) }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('documentos.partials.formEmitidos', [
                                                'documento' => $documento,
                                            ])

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de eliminación -->
                        <div class="modal fade" id="deleteModal-{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel-{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg"">
                                <div class="modal-content">
                                    <form action="{{ route('documentos_emitidos.destroy', $documento->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel-{{ $documento->id }}">Eliminar
                                                Documento</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea eliminar el documento
                                                <strong>{{ ucfirst($documento->nombre_doc) }} </strong>?
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!--Modal de historicos-->
                        <div class="modal fade" id="historialModal{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="historialModalLabel{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="historialModalLabel{{ $documento->id }}">
                                            Historial del Documento <strong>{{ ucfirst($documento->nombre_doc) }} </strong>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Resumen del documento -->
                                        <div class="card mb-4 shadow-sm">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-file-earmark-text-fill me-2"></i> Resumen del Documento
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        <i class="bi bi-card-text me-2 text-primary"></i>
                                                        <strong>Nombre del Documento:</strong>
                                                        <span
                                                            class="text-muted">{{ ucfirst($documento->nombre_doc) }}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="bi bi-person-fill me-2 text-primary"></i>
                                                        <strong>Para:</strong>
                                                        <span class="text-muted">{{ $documento->destino }}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="bi bi-building me-2 text-primary"></i>
                                                        <strong>Entidad:</strong>
                                                        <span
                                                            class="text-muted">{{ $documento->entidad->nombre ?? 'Sin entidad asignada' }}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="bi bi-calendar-event-fill me-2 text-primary"></i>
                                                        <strong>Fecha de Emisión:</strong>
                                                        <span class="text-muted">{{ $documento->fecha_recibido }}</span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <i class="bi bi-envelope-fill me-2 text-primary"></i>
                                                        <strong>Asunto:</strong>
                                                        <span class="text-muted">{{ $documento->asunto }}</span>
                                                    </li>

                                                    <li class="list-group-item">
                                                        <i class="bi bi-chat-left-dots-fill me-2 text-primary"></i>
                                                        <strong>Observaciones:</strong>
                                                        <span
                                                            class="text-muted">{{ $documento->observaciones ?? 'Sin observaciones' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Aquí va el contenido del historial del documento -->
                                        @php
                                            $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                        @endphp
                                        <!-- Historial del Documento -->
                                        @if ($historicosDocumento->isEmpty())
                                            <p class="text-muted">No hay historial para este documento.</p>
                                        @else
                                            <ul class="timeline list-unstyled">
                                                @foreach ($historicosDocumento as $historico)
                                                    <li class="timeline-item mb-4">
                                                        <div class="card border-light shadow-sm">
                                                            <div
                                                                class="card-header bg-light text-dark d-flex justify-content-between">
                                                                <span><i
                                                                        class="bi bi-clock me-2"></i>{{ $historico->fecha_cambio }}</span>
                                                                <span
                                                                    class="badge bg-{{ $loop->index % 2 == 0 ? 'success' : 'warning' }}">
                                                                    {{ $historico->estado_nuevo ?? 'Sin cambio' }}
                                                                </span>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="mb-1"><strong>Atendido por:</strong>
                                                                    {{ $historico->usuario->nombre }}
                                                                    {{ $historico->usuario->apellido }}</p>
                                                                @if ($historico->destinatarioUser)
                                                                    <p class="mb-1"><strong>Derivado a:</strong>
                                                                        {{ $historico->destinatarioUser->nombre }}
                                                                        {{ $historico->destinatarioUser->apellido }}</p>
                                                                @endif
                                                                @if ($historico->estado_anterior)
                                                                    <p class="mb-1"><strong>Estado Anterior:</strong>
                                                                        {{ $historico->estado_anterior }}</p>
                                                                @endif
                                                                <p class="mb-1"><strong>Observaciones:</strong>
                                                                    {{ $historico->observaciones ?? 'Sin observaciones' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--final historicos-->
                        <!-- Modal de Asignar -->
                        <div class="modal fade" id="asignarModal{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="asignarModalLabel{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form method="POST" action="{{ route('historial.asignarEmitidos', $documento->id) }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="asignarModalLabel{{ $documento->id }}">
                                                Asignar
                                                Destinatario a {{ ucfirst($documento->nombre_doc) }} </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header bg-secondary text-white">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-file-earmark-text-fill me-2"></i> Resumen del
                                                        Documento
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">
                                                            <i class="bi bi-card-text me-2 text-primary"></i>
                                                            <strong>Nombre del Documento:</strong>
                                                            <span
                                                                class="text-muted">{{ ucfirst($documento->nombre_doc) }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <i class="bi bi-person-fill me-2 text-primary"></i>
                                                            <strong>Para:</strong>
                                                            <span class="text-muted">{{ $documento->destino }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <i class="bi bi-building me-2 text-primary"></i>
                                                            <strong>Entidad:</strong>
                                                            <span
                                                                class="text-muted">{{ $documento->entidad->nombre ?? 'Sin entidad asignada' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <i class="bi bi-calendar-event-fill me-2 text-primary"></i>
                                                            <strong>Fecha de Emisión:</strong>
                                                            <span
                                                                class="text-muted">{{ $documento->fecha_recibido }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <i class="bi bi-envelope-fill me-2 text-primary"></i>
                                                            <strong>Asunto:</strong>
                                                            <span class="text-muted">{{ $documento->asunto }}</span>
                                                        </li>

                                                        <li class="list-group-item">
                                                            <i class="bi bi-chat-left-dots-fill me-2 text-primary"></i>
                                                            <strong>Observaciones:</strong>
                                                            <span
                                                                class="text-muted">{{ $documento->observaciones ?? 'Sin observaciones' }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Destinatario</label>
                                                    <select name="destinatario_id" class="form-select" required>
                                                        <option value="">Seleccione un destinatario</option>
                                                        @foreach ($users as $user)
                                                            @if ($user->id !== auth()->id())
                                                                <option value="{{ $user->id }}">{{ $user->nombre }}
                                                                    {{ $user->apellido }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Estado Nuevo</label>
                                                    <select name="estado_nuevo" class="form-select" required>
                                                        <option value="por firma">Por firma</option>
                                                        <option value="firmado">Firmado</option>
                                                        <option value="observado">Observado</option>
                                                        <option value="por respuesta">Por respuesta</option>
                                                        <option value="para oficio">Para oficio</option>
                                                        <option value="atendido">Atendido</option>
                                                        <option value="archivado">Archivado</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Observaciones</label>
                                                <textarea name="observaciones" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary">Asignar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="estadoModal{{ $documento->id }}" tabindex="-1"
                            aria-labelledby="estadoModalLabel{{ $documento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST" action="{{ route('historial.asignarEmitidos', $documento->id) }}">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="asignarModalLabel{{ $documento->id }}">
                                                Cambiar estado a {{ ucfirst($documento->nombre_doc) }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Estado Nuevo</label>
                                                <select name="estado_nuevo" class="form-select" required>
                                                    <option value="por firma">Por firma</option>
                                                    <option value="firmado">Firmado</option>
                                                    <option value="observado">Observado</option>
                                                    <option value="por respuesta">Por respuesta</option>
                                                    <option value="para oficio">Para oficio</option>
                                                    <option value="atendido">Atendido</option>
                                                    <option value="archivado">Archivado</option>
                                                    <option value="otro">Otro</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Observaciones</label>
                                                <textarea name="observaciones" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary">Asignar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--Final modal asignar-->

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay documentos registrados.</td>
                        </tr>
                    @endforelse



                </tbody>
            </table>
            <!-- Paginación -->
            <style>
                .card {
                    border-width: 2px;
                    border-radius: 8px;
                }

                .card-header {
                    font-size: 1rem;
                    font-weight: bold;
                }

                .card-footer {
                    font-size: 0.85rem;
                    background-color: #f8f9fa;
                }
            </style>
            <script>
                // Inicializar todos los popovers en la página
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            </script>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $documentos->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal de creación -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('documentos_emitidos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Nuevo Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('documentos.partials.formEmitidos', ['documento' => null])

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Agregar en la sección <head> o antes de cerrar </body> -->
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <!--Script Select2-->
    @php
        $entidadesArray = $entidades->map(function ($entidad) {
            return [
                'id' => $entidad->id,
                'nombre' => $entidad->nombre,
                'siglas' => $entidad->siglas,
            ];
        });
    @endphp

    <script>
        // Pasamos los datos de Laravel a JavaScript
        window.entidades = @json($entidadesArray);
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function initAutocomplete(modal) {
                const searchInput = modal.querySelector(".entidad_search");
                const hiddenInput = modal.querySelector(".entidad_id");
                const dropdown = modal.querySelector(".entidad_dropdown");

                if (!searchInput || !hiddenInput || !dropdown) return;

                // Mostrar la entidad actual al abrir el modal
                const entidadSeleccionada = window.entidades.find(e => e.id == hiddenInput.value);
                if (entidadSeleccionada) {
                    searchInput.value = `${entidadSeleccionada.nombre} (${entidadSeleccionada.siglas})`;
                }

                searchInput.addEventListener("input", function() {
                    const searchTerm = searchInput.value.toLowerCase();
                    dropdown.innerHTML = ""; // Limpiar opciones previas

                    if (searchTerm.length === 0) {
                        dropdown.style.display = "none";
                        return;
                    }

                    // Filtrar entidades por nombre o siglas
                    const filteredEntidades = window.entidades.filter(entidad =>
                        entidad.nombre.toLowerCase().includes(searchTerm) ||
                        entidad.siglas.toLowerCase().includes(searchTerm)
                    );

                    if (filteredEntidades.length === 0) {
                        dropdown.style.display = "none";
                        return;
                    }

                    // Crear las opciones de la lista desplegable
                    filteredEntidades.forEach(entidad => {
                        const option = document.createElement("div");
                        option.classList.add("dropdown-item");
                        option.textContent = `${entidad.nombre} (${entidad.siglas})`;
                        option.setAttribute("data-id", entidad.id);

                        option.addEventListener("click", function() {
                            searchInput.value = entidad.nombre + " (" + entidad.siglas +
                                ")";
                            hiddenInput.value = entidad
                                .id; // Guardamos el ID real en el input oculto
                            dropdown.style.display = "none"; // Ocultamos el dropdown
                        });

                        dropdown.appendChild(option);
                    });

                    dropdown.style.display = "block"; // Mostramos la lista desplegable
                });

                // Ocultar el dropdown si se hace clic fuera
                document.addEventListener("click", function(event) {
                    if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.style.display = "none";
                    }
                });
            }

            // Inicializar el autocompletado cuando se abran los modales
            document.getElementById("createModal").addEventListener("shown.bs.modal", function() {
                initAutocomplete(this);
            });

            document.querySelectorAll("div[id^='editModal-']").forEach(modal => {
                modal.addEventListener("shown.bs.modal", function() {
                    initAutocomplete(this);
                });
            });

            document.querySelectorAll("div[id^='respuestaModal-']").forEach(modal => {
                modal.addEventListener("shown.bs.modal", function() {
                    initAutocomplete(this);
                });
            });
        });
    </script>


    <!-- Alertas -->
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: "{{ implode(', ', $errors->all()) }}",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
        </script>
    @endif



    <script src="{{ asset('js/popover.js') }}"></script>

@endsection
