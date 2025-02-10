@extends('layouts.app')

@section('content')
    <div class="container-fluid fluid bg-white p-4 rounded shadow ">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-bold text-dark">Historial de Documentos</h3>
            <!-- Botón para abrir el modal de creación -->
            <a href="{{ url('/exportar-historial') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
            </a>
        </div>
        <form method="GET" action="{{ route('historial.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="numero_oficio" class="form-control"
                        placeholder="Buscar por número de oficio" value="{{ request('numero_oficio') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_cambio" class="form-control" placeholder="Buscar por fecha de cambio"
                        value="{{ request('fecha_cambio') }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="remitente_destino" class="form-control"
                        placeholder="Buscar por remitente o destino" value="{{ request('remitente_destino') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('historial.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive"> <!-- Agrega este contenedor -->

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre Documento</th>
                        <th>Remitente/Destino</th>
                        <th>Usuario</th>
                        <th>Estado Anterior</th>
                        <th>Estado Nuevo</th>
                        <th>Fecha Cambio</th>
                        <th>Observaciones</th>
                        <th>Origen</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($historial->isNotEmpty())
                        @foreach ($historial as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ ucfirst($item->documento?->nombre_doc ?? ($item->documentoEmitido?->nombre_doc ?? 'N/A')) }}
                                </td>
                                <td>
                                    @if ($item->origen === 'emitido')
                                        {{ $item->documentoEmitido?->destino ?? 'N/A' }}
                                    @else
                                        {{ $item->documento?->remitente ?? 'N/A' }}
                                    @endif
                                </td>

                                <td>
                                    {{ $item->usuario?->nombre ?? 'N/A' }} {{ $item->usuario?->apellido ?? '' }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary"
                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                        {{ $item->estado_anterior }}
                                    </button>

                                </td>
                                <td>{{ $item->estado_nuevo }}</td>
                                <td>{{ $item->fecha_cambio }}</td>
                                <td>{{ $item->observaciones }}</td>
                                <td>
                                    @if ($item->origen === 'emitido')
                                        <button type="button" class="btn btn-outline-success">{{ $item->origen }}</button>
                                    @else
                                        <button type="button" class="btn btn-outline-danger">{{ $item->origen }}</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No hay documentos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $historial->links('vendor.pagination.bootstrap-5') }}
        </div>
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
    </div>
@endsection
