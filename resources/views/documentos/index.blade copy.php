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
                <th>Descripción</th>
                <th>Archivo</th>
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

                    <td>{{ $documento->descripcion }}</td>
                    <td>
                        @if ($documento->archivo)
                            @php
                                $extension = pathinfo($documento->archivo, PATHINFO_EXTENSION);
                            @endphp
                            <!-- Si el archivo es PDF -->
                            @if ($extension == 'pdf')
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="bi bi-filetype-pdf" style="font-size: 24px; color: red;"></i>
                                </a>
                                <!-- Si el archivo es DOCX -->
                            @elseif ($extension == 'docx')
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="bi bi-file-earmark-word-fill" style="font-size: 24px; color: blue;"></i>
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="fa fa-file" style="font-size: 24px;"></i> Archivo
                                </a>
                            @endif
                        @else
                            No hay archivo
                        @endif
                    </td>
                    <td>
                        <!-- Botón de Editar -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $documento->id_documento }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- Botón de Eliminar que activa el modal -->
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $documento->id_documento }}">
                            <i class="bi bi-archive-fill"></i>
                        </button>

                        <!--Descargar-->
                        @if ($documento->archivo)
                            @php
                                $extension = pathinfo($documento->archivo, PATHINFO_EXTENSION);
                            @endphp
                            <!-- Si el archivo es PDF -->
                            @if ($extension == 'pdf')
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="bi bi-filetype-pdf" style="font-size: 24px; color: red;"></i>
                                </a>
                                <!-- Si el archivo es DOCX -->
                            @elseif ($extension == 'docx')
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="bi bi-file-earmark-word-fill" style="font-size: 24px; color: blue;"></i>
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                    <i class="fa fa-file" style="font-size: 24px;"></i> Archivo
                                </a>
                            @endif
                        @else
                            <a>
                                <i class="bi bi-filetype-pdf" style="font-size: 24px; color: rgb(129, 129, 129);"></i>
                            </a>
                        @endif

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
                                    <div class="mb-3">
                                        <label for="numero_oficio" class="form-label">Número de Oficio</label>
                                        <input type="text" class="form-control" id="numero_oficio" name="numero_oficio"
                                            value="{{ $documento->numero_oficio }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fecha_recepcion" class="form-label">Fecha de Recepción</label>
                                        <input type="date" class="form-control" id="fecha_recepcion"
                                            name="fecha_recepcion" value="{{ $documento->fecha_recepcion }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="remitente" class="form-label">Remitente</label>
                                        <input type="text" class="form-control" id="remitente" name="remitente"
                                            value="{{ $documento->remitente }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tipo" class="form-label">Tipo</label>
                                        <input type="text" class="form-control" id="tipo" name="tipo"
                                            value="{{ $documento->tipo }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" required>{{ $documento->descripcion }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones">{{ $documento->observaciones }}</textarea>
                                    </div>

                                    <!-- Mostrar el archivo actual -->
                                    @if ($documento->archivo)
                                        <div class="mb-3">
                                            <label class="form-label">Archivo Actual</label><br>
                                            <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                                <i class="fa fa-file-pdf-o" style="font-size: 24px; color: red;"></i> PDF
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Campo para subir archivo nuevo -->
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
                            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion"
                                required>
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

    <!-- Modal de Confirmación de Eliminación -->
    @foreach ($documentos as $documento)
        <div class="modal fade" id="deleteModal{{ $documento->id_documento }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $documento->id_documento }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $documento->id_documento }}">Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar este documento? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <!-- Botón para cancelar -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                        <!-- Formulario de eliminación -->
                        <form action="{{ route('documentos.destroy', $documento->id_documento) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach



    <!---Alert--->
    {{-- Mensajes de SweetAlert2 --}}
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

    {{-- bloque de los errores del formulario --}}
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
@endsection
