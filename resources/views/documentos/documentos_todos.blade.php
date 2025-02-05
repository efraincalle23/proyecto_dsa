@extends('layouts.app')

@section('content')
    <div class="container-fluid fluid bg-white p-4 rounded shadow ">
        <h3 class="mb-4">Documentos</h3>

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
        <form method="GET" action="{{ route('documentos_todos.index') }}" class="mb-4">
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
                    <input type="text" name="destino" class="form-control" placeholder="Buscar por destinatario"
                        value="{{ request('destino') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('documentos_todos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-reset"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabla de documentos -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de Oficio</th>
                    <th>Destino/Remitente</th>
                    <th>Entidad</th>
                    <th>Asunto</th>
                    <th>Fecha Recepción</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documentos as $documento)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $documento->nombre_doc }}</td>
                        <td>{{ $documento->destino }}</td>
                        <td>{{ $documento->entidad->siglas ?? 'Sin entidad' }}</td>
                        <td>{{ $documento->asunto }}</td>
                        <td>{{ $documento->fecha_recibido }}</td>
                        <td>
                            @if ($documento->tipo == 'emitido')
                                <button type="button" class="btn btn-outline-primary"
                                    style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                    {{ ucfirst($documento->tipo) }}
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary"
                                    style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                    {{ ucfirst($documento->tipo) }}
                                </button>
                            @endif
                        </td>
                        <td>

                            <button type="button" class="btn {{ $documento->eliminado ? 'btn-danger' : 'btn-success' }}"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                {{ $documento->eliminado ? 'Eliminado' : 'Activo' }}
                            </button>
                        </td>
                        <td>
                            <!-- Botón para abrir el modal del historial -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#historialModal{{ $documento->id }}">
                                <i class="bi bi-clock-history" style="font-style: normal;"> Historial</i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de Historial -->
                    <div class="modal fade" id="historialModal{{ $documento->id }}" tabindex="-1"
                        aria-labelledby="historialModalLabel{{ $documento->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="historialModalLabel{{ $documento->id }}">
                                        Historial del Documento N° {{ $documento->nombre_doc }}
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
                                    @if ($documento->historialDocumento->isEmpty())
                                        <p class="text-muted">No hay historial para este documento.</p>
                                    @else
                                        <ul class="list-group">
                                            @foreach ($documento->historialDocumento as $historial)
                                                <li class="list-group-item">
                                                    <strong>Fecha:</strong> {{ $historial->fecha_cambio }}<br>
                                                    <strong>Estado Anterior:</strong> {{ $historial->estado_anterior }}<br>
                                                    <strong>Estado Nuevo:</strong> {{ $historial->estado_nuevo }}<br>
                                                    <strong>Observaciones:</strong>
                                                    {{ $historial->observaciones ?? 'Sin observaciones' }}
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
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay documentos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $documentos->links('vendor.pagination.bootstrap-5') }}
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
@endsection
