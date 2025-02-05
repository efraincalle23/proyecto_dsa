@extends('layouts.app')

@section('content')
<h1>
    Documentos </h2>

    <!-- Botón para abrir el modal de creación -->
    @role('Administrador|Jefe DSA')
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        Agregar Documento
    </button>
    @endrole
    {{-- Filtros de búsqueda --}}
    <form method="GET" action="{{ route('documentos.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="numero_oficio" class="form-control" placeholder="Buscar por numero"
                    value="{{ request('numero_oficio') }}">
            </div>
            <div class="col-md-4">
                <input type="date" name="fecha_recepcion" class="form-control" placeholder="Buscar por fecha_recepcion"
                    value="{{ request('fecha_recepcion') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="remitente" class="form-control" placeholder="Buscar por remitente"
                    value="{{ request('remitente') }}">
            </div>

        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-secondary me-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="{{ route('documentos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-reset"></i> Limpiar
                </a>
            </div>
        </div>
    </form>
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
                <th>Documento Matriz</th>
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
                <td>{{ ($documento->documentoPadre->tipo ?? '--') . ' - ' . ($documento->documentoPadre->numero_oficio ?? '--') }}
                </td>


                <td>

                    <!--Descargar archivo-->
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
                    <!-- Botón de Editar -->
                    @role('Administrador|Jefe DSA')
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                        data-bs-target="#editModal{{ $documento->id_documento }}">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <!-- Botón de Eliminar que activa el modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal{{ $documento->id_documento }}">
                        <i class="bi bi-archive-fill"></i>
                    </button>
                    @endrole

                    <!-- Botón de Asignar -->
                    <!-- Botón de Asignar -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#asignarModal{{ $documento->id_documento }}">
                        <i class="bi bi-person-plus"></i> Asignar
                    </button>

                    <!-- Botón de Historial -->
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#historialModal{{ $documento->id_documento }}">
                        <i class="bi bi-clock-history"></i> Historial
                    </button>

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
                                <h5 class="modal-title" id="editModalLabel{{ $documento->id_documento }}">
                                    Editar
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
                                    <input type="date" class="form-control" id="fecha_editar" name="fecha_recepcion"
                                        value="{{ $documento->fecha_recepcion }}" required>
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
                                    <textarea class="form-control" id="descripcion" name="descripcion"
                                        required>{{ $documento->descripcion }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones"
                                        name="observaciones">{{ $documento->observaciones }}</textarea>
                                </div>
                                <!--Modal documento padre -->
                                <div class="mb-3">
                                    <label for="documento_padre_id" class="form-label">Documento Padre</label>
                                    <select name="documento_padre_id" id="documento_padre_id" class="form-select">
                                        <option value="">Sin documento padre</option>
                                        @foreach ($documentos as $doc)
                                        <!-- Evitar que un documento sea su propio padre -->
                                        @if ($doc->id_documento !== $documento->id_documento)
                                        <option value="{{ $doc->id_documento }}"
                                            {{ $documento->documento_padre_id == $doc->id_documento ? 'selected' : '' }}>
                                            {{ $doc->numero_oficio }} - {{ $doc->descripcion }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Mostrar el archivo actual -->
                                @if ($documento->archivo)
                                <div class="mb-3">
                                    <label class="form-label">Archivo Actual</label><br>
                                    <a href="{{ asset('storage/' . $documento->archivo) }}" target="_blank">
                                        <i class="fa fa-file-pdf-o" style="font-size: 24px; color: red;"></i>
                                        PDF
                                    </a>
                                </div>
                                @endif

                                <!-- Campo para subir archivo nuevo -->
                                <div class="mb-3">
                                    <label for="archivo" class="form-label">Subir Nuevo Archivo (PDF,
                                        DOCX)</label>
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
            <!-- Modal de Asignar -->
            <div class="modal fade" id="asignarModal{{ $documento->id_documento }}" tabindex="-1"
                aria-labelledby="asignarModalLabel{{ $documento->id_documento }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('historicos.asignar', $documento->id_documento) }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="asignarModalLabel{{ $documento->id_documento }}">
                                    Asignar
                                    Destinatario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Información del Documento -->
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Documento</label>
                                    <input type="text" class="form-control" value="{{ $documento->tipo }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Número de Oficio</label>
                                    <input type="text" class="form-control" value="{{ $documento->numero_oficio }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Remitente</label>
                                    <input type="text" class="form-control" value="{{ $documento->remitente }}"
                                        disabled>
                                </div>
                                <!-- Campos para Asignación -->
                                <div class="mb-3">
                                    <label class="form-label">Destinatario</label>
                                    <select name="destinatario_id" class="form-select" required>
                                        <option value="">Seleccione un destinatario</option>
                                        @foreach ($users as $user)
                                        @if ($user->id !== auth()->id())
                                        <!-- Excluir al usuario autenticado -->
                                        <option value="{{ $user->id }}"> {{ $user->nombre }}
                                            {{ $user->apellido }}
                                        </option>
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
            <!-- Modal de Historial -->
            <div class="modal fade" id="historialModal{{ $documento->id_documento }}" tabindex="-1"
                aria-labelledby="historialModalLabel{{ $documento->id_documento }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="historialModalLabel{{ $documento->id_documento }}">
                                Historial
                                del Documento
                                <strong>{{ $documento->tipo }}
                                    N°
                                    {{ $documento->numero_oficio }}</strong>

                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            @php
                            $historicosDocumento = $documento->historicos; // Ya viene ordenado desde el controlador
                            @endphp

                            @if ($historicosDocumento->isEmpty())
                            <p class="text-muted">No hay historial para este documento.</p>
                            @else
                            <div class="row">
                                @foreach ($historicosDocumento as $historico)
                                <div class="col-12 mb-3">
                                    <div class="card border-{{ $loop->index % 2 == 0 ? 'warning' : 'success' }}">
                                        <div
                                            class="card-header bg-{{ $loop->index % 2 == 0 ? 'warning' : 'success' }} text-white">
                                            <strong>{{ $historico->fecha_cambio }}</strong>
                                            <span
                                                class="badge bg-light text-dark float-end">{{ $historico->estado_nuevo }}</span>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Remitente:</strong>
                                                {{ $historico->documento->remitente }}</p>
                                            <p><strong>Atendido por:</strong>
                                                {{ $historico->usuario->nombre }}
                                                {{ $historico->usuario->apellido }}</p>
                                            @if ($historico->destinatarioUser)
                                            <p><strong>Derivado a:</strong>
                                                {{ $historico->destinatarioUser->nombre }}
                                                {{ $historico->destinatarioUser->apellido }}</p>
                                            @endif
                                            @if ($historico->estado_anterior)
                                            <p><strong>Estado Anterior:</strong>
                                                {{ $historico->estado_anterior }}</p>
                                            @endif
                                            <p><strong>Observaciones:</strong>
                                                {{ $historico->observaciones ?? 'Sin observaciones' }}
                                            </p>
                                        </div>
                                        <div class="card-footer text-muted">
                                            <i class="bi bi-clock"></i> Última modificación:
                                            {{ $historico->fecha_cambio }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
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
    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $documentos->links('vendor.pagination.bootstrap-5') }}
    </div>

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
                                value="{{ date('Y-m-d') }}" required>
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
                            <label for="documento_padre_id" class="form-label">Documento Padre</label>
                            <select name="documento_padre_id" id="documento_padre_id" class="form-select">
                                <option value="">Sin documento padre</option>
                                @foreach ($documentos as $doc)
                                <option value="{{ $doc->id_documento }}"
                                    {{ old('documento_padre_id', $documento->documento_padre_id ?? '') == $doc->id_documento ? 'selected' : '' }}>
                                    {{ $doc->numero_oficio }} - {{ $doc->descripcion }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Subir Archivo (PDF, DOCX)</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" accept=".pdf,.docx">
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
                    <h5 class="modal-title" id="deleteModalLabel{{ $documento->id_documento }}">Confirmar
                        Eliminación
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @endsection