@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Históricos</h1>

        <!-- Mensajes de éxito/error -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filtros -->
        <div class="container mb-4">
            <form method="GET" action="{{ route('historicos.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="remitente" class="form-label">Remitente</label>
                    <input type="text" name="remitente" id="remitente" class="form-control"
                        value="{{ request('remitente') }}" placeholder="Buscar por remitente">
                </div>
                <div class="col-md-4">
                    <label for="numero_oficio" class="form-label">Número de Oficio</label>
                    <input type="text" name="numero_oficio" id="numero_oficio" class="form-control"
                        value="{{ request('numero_oficio') }}" placeholder="Buscar por número de oficio">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Buscar</button>
                    <a href="{{ route('historicos.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Historial -->
        <ul class="list-group">
            @foreach ($historicos as $historico)
                <li class="list-group-item mb-3">
                    <div class="d-flex justify-content-between">
                        <!-- Información Principal -->
                        <div>
                            <h5><i class="bi bi-clock-history"></i> {{ $historico->fecha_cambio }}</h5>
                            <p><strong>{{ $historico->documento->id_documento }}{{ $historico->documento->tipo }} N°
                                    {{ $historico->documento->numero_oficio }}</strong></p>
                            <p><strong>Remitente:</strong> {{ $historico->documento->remitente }}</p>
                            <p><strong>Descripcion:</strong> {{ $historico->documento->descripcion }}</p>
                            <p><strong>Atendido por:</strong> {{ $historico->usuario->nombre }}
                                {{ $historico->usuario->apellido }} <span
                                    class="badge bg-secondary">{{ $historico->estado_anterior }}</span>
                                <strong>derivado a:</strong>
                                @if ($historico->destinatarioUser)
                                    {{ $historico->destinatarioUser->nombre }}
                                    {{ $historico->destinatarioUser->apellido }}
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                                <span class="badge bg-primary">{{ $historico->estado_nuevo }}</span>
                            </p>

                            @if ($historico->observaciones)
                                <p><strong>Observaciones:</strong> {{ $historico->observaciones }}</p>
                            @endif
                        </div>
                        <!-- Botón de Acción -->
                        <div class="align-self-start">
                            <!-- Botón para abrir el modal -->
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#asignarModal{{ $historico->id }}">Asignar</button>
                        </div>
                    </div>
                </li>

                <!-- Modal -->
                <div class="modal fade" id="asignarModal{{ $historico->id }}" tabindex="-1"
                    aria-labelledby="asignarModalLabel{{ $historico->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('historicos.asignar2', $historico->id) }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="asignarModalLabel{{ $historico->id }}">Asignar Destinatario
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Información del Documento -->
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Documento</label>
                                        <input type="text" class="form-control"
                                            value="{{ $historico->documento->tipo }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Número de Oficio</label>
                                        <input type="text" class="form-control"
                                            value="{{ $historico->documento->numero_oficio }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Remitente</label>
                                        <input type="text" class="form-control"
                                            value="{{ $historico->documento->remitente }}" disabled>
                                    </div>
                                    <!-- Campos para Asignación -->
                                    <div class="mb-3">
                                        <label class="form-label">Destinatario</label>
                                        <select name="destinatario" class="form-select" required>
                                            <option value="">Seleccione un destinatario</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->nombre }}
                                                    {{ $user->apellido }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Estado Nuevo</label>
                                        <select name="estado_nuevo" class="form-select" required>
                                            <option value="por firma">Por firma</option>
                                            <option value="observado">Observado</option>
                                            <option value="en proceso">En proceso</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea name="observaciones" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Asignar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </ul>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $historicos->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
