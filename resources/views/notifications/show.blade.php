@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>{{ $notification->data['message'] }}</h4>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><strong>Detalles del Documento</strong></span>
                <p class="mb-0"><strong>Fechaa:</strong>
                    {{ \Carbon\Carbon::parse($notification->data['fecha_notificacion'])->tz('America/Lima')->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="card-body">
                <p><strong>Documento:</strong> {{ $notification->data['nombre_documento'] }}</p>
                <p><strong>Asunto:</strong> {{ $notification->data['asunto'] }}</p>
                <p><strong>Remitente:</strong> {{ $notification->data['usuario'] }}</p>
                <p><strong>Fecha de Recepción del Documento:</strong>
                    {{ $notification->data['fecha_recibido'] ? \Carbon\Carbon::parse($notification->data['fecha_recibido'])->format('d/m/Y H:i') : 'No registrada' }}
                </p>
                <p><strong>Observaciones:</strong> {{ $notification->data['observaciones'] }}</p>
                <p><strong>Requierimiento:</strong> {{ $notification->data['estado'] }}</p>
                @if ($notification->data['origen'] == 'emitido')
                    <form method="GET" action="{{ route('documentos_emitidos.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="numero_oficio" class="form-control"
                                    placeholder="Buscar por número de oficio"
                                    value="{{ $notification->data['numero_oficio'] }}" hidden>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="fas fa-search"></i> Atender
                        </button>
                    </form>
                @else
                    <form method="GET" action="{{ route('documentos_recibidos.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="numero_oficio" class="form-control"
                                    placeholder="Buscar por número de oficio"
                                    value="{{ $notification->data['numero_oficio'] }}" hidden>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-secondary me-2">
                            <i class="fas fa-search"></i> Atender
                        </button>
                    </form>
                @endif
            </div>

        </div>
        <!-- Opción para volver a la lista de notificaciones -->
        <a href="{{ route('notifications.index') }}" class="btn btn-primary mt-3">Volver a Notificaciones</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
