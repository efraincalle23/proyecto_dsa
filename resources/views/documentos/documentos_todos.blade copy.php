@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Documentos</h1>

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
                <div class="col-md-3">
                    <input type="text" name="destino" class="form-control" placeholder="Buscar por destinatario"
                        value="{{ request('destino') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
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
                        <td>{{ $documento->numero_oficio }}</td>
                        <td>{{ $documento->destino }}</td>
                        <td>{{ $documento->asunto }}</td>
                        <td>{{ $documento->fecha_recibido }}</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ ucfirst($documento->tipo) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $documento->eliminado ? 'bg-danger' : 'bg-success' }}">
                                {{ $documento->eliminado ? 'Eliminado' : 'Activo' }}
                            </span>
                        </td>
                        <td>
                            <!-- Botón para abrir el modal del historial -->
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#historialModal{{ $documento->id }}">
                                <i class="bi bi-clock-history"></i> Historial
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
                                        Historial del Documento N° {{ $documento->numero_oficio }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
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
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
