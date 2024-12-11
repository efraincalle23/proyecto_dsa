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

        <!-- Tabla de Históricos -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Número de Oficio</th>
                    <th>Remitente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historicos as $historico)
                    <tr>
                        <td>{{ $historico->id }}</td>
                        <td>{{ $historico->documento->descripcion }}</td>
                        <td>{{ $historico->documento->numero_oficio }}</td>
                        <td>{{ $historico->documento->remitente }}</td>
                        <td>{{ $historico->estado_nuevo ?? $historico->estado_anterior }}</td>
                        <td>
                            <!-- Botón para abrir el modal -->
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#asignarModal{{ $historico->id }}">Asignar</button>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="asignarModal{{ $historico->id }}" tabindex="-1"
                        aria-labelledby="asignarModalLabel{{ $historico->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('historicos.asignar', $historico->id) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="asignarModalLabel{{ $historico->id }}">Asignar
                                            Destinatario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre del Documento</label>
                                            <input type="text" class="form-control"
                                                value="{{ $historico->documento->descripcion }}" disabled>
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
                                        <div class="mb-3">
                                            <label class="form-label">Destinatario</label>
                                            <select name="destinatario" class="form-control" data-live-search="true">
                                                <option value="">Seleccione un destinatario</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select name="estado_nuevo" class="form-control" required>
                                                <option value="por firma">Por firma</option>
                                                <option value="observado">Observado</option>
                                                <option value="en proceso">En proceso</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Observaciones</label>
                                            <textarea name="observaciones" class="form-control"></textarea>
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
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        {{ $historicos->links() }}
    </div>
@endsection
