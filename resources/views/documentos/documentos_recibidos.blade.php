@extends('layouts.app')

@section('content')
    <div class="container-fluid fluid bg-white p-4 rounded shadow ">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-bold text-dark">Documentos Recibidos</h3>
            <!-- Botón para abrir el modal de creación -->
            <button type="button" class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
                Nuevo Documento
            </button>
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

        {{-- Filtros de búsqueda --}}
        <form method="GET" action="{{ route('documentos_recibidos.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="numero_oficio" class="form-control"
                        placeholder="Buscar por número de oficio" value="{{ request('numero_oficio') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_recibido" class="form-control"
                        placeholder="Buscar por fecha de recepción" value="{{ request('fecha_recibido') }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="remitente" class="form-control" placeholder="Buscar por remitente"
                        value="{{ request('remitente') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('documentos_recibidos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-reset"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabla de documentos recibidos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de Oficio</th>
                    <th>Remitente</th>
                    <th>Asunto</th>
                    <th>Recibido</th>
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
                        <td>{{ ucfirst($documento->nombre_doc) }} </td>
                        <td>{{ $documento->remitente }}</td>
                        <td>
                            <!--Asunto-->
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


                                <button type="button" class="btn btn-outline-secondary"
                                    style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                    {{ $ultimoHistorial->estado_nuevo ?? 'Sin cambio' }}
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


                                <button type="button" class="btn btn-success btn-sm popover-trigger" title="Ultimo estado"
                                    data-bs-content="   
                                    @if ($ultimoHistorial->destinatarioUser) {{ $ultimoHistorial->usuario->nombre ?? 'N/A' }}
                                        {{ $ultimoHistorial->usuario->apellido ?? '' }} derivó a
                                        {{ $ultimoHistorial->destinatarioUser->nombre }} {{ $ultimoHistorial->destinatarioUser->apellido }}  
                                      @if ($ultimoHistorial->estado_nuevo)
                                        ({{ $ultimoHistorial->estado_nuevo ?? 'S/A' }})-Observ: {{ $ultimoHistorial->observaciones ?? 'S/O' }} @endif
@else
{{ $ultimoHistorial->estado_nuevo ?? 'S/A' }} por {{ $ultimoHistorial->usuario->apellido }}
                                    {{ $ultimoHistorial->usuario->nombre }} @endif">
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
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal-{{ $documento->id }}">
                                <i class="bi bi-pencil-square"></i> <!-- Ícono de editar -->
                            </button>

                            <!-- Botón para abrir el modal de eliminación -->
                            @if ($documento->eliminado)
                            @else
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal-{{ $documento->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                            <!-- Mostrar opción de restaurar si está eliminado -->
                            @if ($documento->eliminado)
                                <form action="{{ route('documentos.restore', $documento->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning btn-sm"><i
                                            class="bi bi-arrow-repeat"></i></button>
                                </form>
                            @endif


                            @if ($documento->eliminado)
                            @else
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#asignarModal{{ $documento->id }}">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#historialModal{{ $documento->id }}">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#respuestaModal-{{ $documento->id }}">
                                <i class="bi bi-reply-all-fill"></i>
                            </button>

                        </td>
                    </tr>

                    <!-- Modal de edición -->
                    <div class="modal fade" id="editModal-{{ $documento->id }}" tabindex="-1"
                        aria-labelledby="editModalLabel-{{ $documento->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('documentos_recibidos.update', $documento->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel-{{ $documento->id }}">Editar Documento
                                            {{ ucfirst($documento->nombre_doc) }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('documentos.partials.form', [
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

                    <!-- Modal de respuesta -->
                    <div class="modal fade" id="respuestaModal-{{ $documento->id }}" tabindex="-1"
                        aria-labelledby="respuestaModal-{{ $documento->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('documentos_emitidos.storeRespuesta', $documento->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="respuestaModalLabel-{{ $documento->id }}">Respuesta a
                                            {{ ucfirst($documento->nombre_doc) }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('documentos.partials.formRespuesta', [
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
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('documentos_recibidos.destroy', $documento->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $documento->id }}">Eliminar
                                            Documento </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Está seguro de que desea eliminar el documento
                                            <strong>{{ ucfirst($documento->nombre_doc) }}</strong>?
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
                                        Historial del Documento <strong>{{ ucfirst($documento->nombre_doc) }}</strong>
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
                                                    <span class="text-muted">{{ ucfirst($documento->nombre_doc) }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="bi bi-person-fill me-2 text-primary"></i>
                                                    <strong>Para:</strong>
                                                    <span class="text-muted">{{ $documento->remitente }}</span>
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

                                    <!-- Historial del Documento -->
                                    @php
                                        $historicosDocumento = $documento->HistorialDocumento ?? collect(); // Evitar error en caso de no haber historiales
                                    @endphp
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
                                                                {{ $historico->observaciones ?? 'Sin observaciones' }}</p>
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
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('historial.asignar', $documento->id) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="asignarModalLabel{{ $documento->id }}">
                                            Asignar
                                            Destinatario a {{ ucfirst($documento->nombre_doc) }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Documento</label>
                                            <input type="text" class="form-control" value="{{ $documento->tipo }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Número de Oficio</label>
                                            <input type="text" class="form-control"
                                                value="{{ $documento->numero_oficio }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Remitente</label>
                                            <input type="text" class="form-control"
                                                value="{{ $documento->remitente }}" disabled>
                                        </div>
                                        <div class="mb-3">
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
                                        <div class="mb-3">
                                            <label class="form-label">Estado Nuevo</label>
                                            <select name="estado_nuevo" class="form-select" required>
                                                <option value="por firma">Por firma</option>
                                                <option value="observado">Observado</option>
                                                <option value="en proceso">En proceso</option>
                                                <option value="atendido">Atendido</option>
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

    <!-- Modal de creación -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('documentos_recibidos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Nuevo Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('documentos.partials.form', ['documento' => null])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/popover.js') }}"></script>

@endsection
