@extends('layouts.app')

@section('content')
    <h1>Gestión de Documentos</h1>

    <!-- Botón para abrir el modal de creación -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        Agregar Documento
    </button>

    <!-- Tabla de documentos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número de Oficio</th>
                <th>Fecha de Recepción</th>
                <th>Remitente</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documentos as $documento)
                <tr>
                    <td>{{ $documento->id_documento }}</td>
                    <td>{{ $documento->numero_oficio }}</td>
                    <td>{{ $documento->fecha_recepcion }}</td>
                    <td>{{ $documento->remitente }}</td>
                    <td>{{ $documento->tipo }}</td>
                    <td>
                        @if ($documento->archivo)
                            <a href="{{ asset('storage/' . $documento->archivo) }}"
                                target="_blank">{{ basename($documento->archivo) }}</a>
                        @else
                            No hay archivo
                        @endif
                    </td>
                    <td>
                        <!-- Botón de Editar -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $documento->id_documento }}">
                            Editar
                        </button>

                        <!-- Formulario de Eliminar -->
                        <form action="{{ route('documentos.destroy', $documento->id_documento) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-archive-fill"></i></button>
                        </form>
                    </td>
                </tr>

                <!-- Modal de Edición -->
                <div class="modal fade" id="editModal{{ $documento->id_documento }}" tabindex="-1"
                    aria-labelledby="editModalLabel{{ $documento->id_documento }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('documentos.update', $documento->id_documento) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $documento->id_documento }}">Editar
                                        Documento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Otros campos de formulario aquí... -->

                                    <!-- Mostrar el archivo actual, si existe -->
                                    @if ($documento->archivo)
                                        <div class="mb-3">
                                            <label class="form-label">Archivo Actual</label><br>
                                            <a href="{{ asset('storage/' . $documento->archivo) }}"
                                                target="_blank">{{ $documento->archivo }}</a>
                                        </div>
                                    @endif

                                    <!-- Campo para subir un archivo nuevo -->
                                    <div class="mb-3">
                                        <label for="archivo" class="form-label">Subir Nuevo Archivo (PDF, DOCX)</label>
                                        <input type="file" class="form-control" id="archivo" name="archivo"
                                            accept=".pdf,.docx">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>

    <!-- Modal de Creación -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="numero_oficio" class="form-label">Número de Oficio</label>
                            <input type="text" class="form-control" id="numero_oficio" name="numero_oficio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_recepcion" class="form-label">Fecha de Recepción</label>
                            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" required>
                        </div>
                        <div class="mb-3">
                            <label for="remitente" class="form-label">Remitente</label>
                            <input type="text" class="form-control" id="remitente" name="remitente" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Subir Archivo (PDF, DOCX)</label>
                            <input type="file" class="form-control" id="archivo" name="archivo"
                                accept=".pdf,.docx">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Documento</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
