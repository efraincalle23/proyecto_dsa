@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-white p-4 rounded shadow-lg">


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="font-weight-bold text-dark">Lista de Entidades UNPRG</h3>
            <!-- Botón para abrir el modal de creación -->
            <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearEntidad">Nueva
                Entidad</button>

        </div>
        <div>
            <form method="GET" action="{{ route('entidades.index') }}" class="mb-4">
                <div class="row g-2">
                    <div class="col-12 col-sm-6 col-md-3">
                        <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre"
                            value="{{ request('nombre') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <input type="text" name="siglas" class="form-control" placeholder="Buscar por siglas"
                            value="{{ request('siglas') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <select name="tipo" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="Órgano de Alta Dirección"
                                {{ request('tipo') == 'Órgano de Alta Dirección' ? 'selected' : '' }}>
                                Órgano de Alta Dirección
                            </option>
                            <option value="Órgano Especial" {{ request('tipo') == 'Órgano Especial' ? 'selected' : '' }}>
                                Órgano Especial
                            </option>
                            <option value="Órgano de Asesoramiento"
                                {{ request('tipo') == 'Órgano de Asesoramiento' ? 'selected' : '' }}>
                                Órgano de Asesoramiento
                            </option>
                            <option value="Órgano de Apoyo" {{ request('tipo') == 'Órgano de Apoyo' ? 'selected' : '' }}>
                                Órgano de Apoyo
                            </option>
                            <option value="Unidad" {{ request('tipo') == 'Unidad' ? 'selected' : '' }}>
                                Unidad
                            </option>
                            <option value="Facultad" {{ request('tipo') == 'Facultad' ? 'selected' : '' }}>
                                Facultad
                            </option>
                            <option value="Escuela Profesional"
                                {{ request('tipo') == 'Escuela Profesional' ? 'selected' : '' }}>
                                Escuela Profesional
                            </option>
                            <option value="Subentidad" {{ request('tipo') == 'Subentidad' ? 'selected' : '' }}>
                                Subentidad
                            </option>
                            <option value="Unidad de Posgrado"
                                {{ request('tipo') == 'Unidad de Posgrado' ? 'selected' : '' }}>
                                Unidad de Posgrado
                            </option>
                            <option value="Combinado" {{ request('tipo') == 'Combinado' ? 'selected' : '' }}>
                                Combinado
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('entidades.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-sync-alt"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive"> <!-- Agrega este contenedor -->

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Siglas</th>
                        <th>Tipo</th>
                        <th>Entidad Superior</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($entidades->isNotEmpty())
                        @foreach ($entidades as $entidad)
                            <tr>
                                <td>{{ $entidad->id }}</td>
                                <td>{{ $entidad->nombre }}</td>
                                <td>{{ $entidad->siglas }}</td>
                                <td>{{ $entidad->tipo }}</td>
                                <td>{{ $entidad->entidadSuperior->nombre ?? 'N/A' }}</td>

                                <td>
                                    <div class="d-flex gap-2 align-items-center">

                                        <!-- Botón para editar -->
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalEditarEntidad{{ $entidad->id }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>


                                        <!-- Botón de Eliminar que activa el modal -->
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $entidad->id }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal para editar -->
                            <div class="modal fade" id="modalEditarEntidad{{ $entidad->id }}" tabindex="-1"
                                aria-labelledby="modalEditarEntidadLabel{{ $entidad->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditarEntidadLabel{{ $entidad->id }}">Editar
                                                Entidad
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('entidades.update', $entidad->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="nombre">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                                        value="{{ $entidad->nombre }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="siglas">Siglas</label>
                                                    <input type="text" class="form-control" id="siglas" name="siglas"
                                                        value="{{ $entidad->siglas }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tipo">Tipo</label>
                                                    <select class="form-control" id="tipo" name="tipo" required>
                                                        <option value="Órgano de Alta Dirección"
                                                            {{ $entidad->tipo == 'Órgano de Alta Dirección' ? 'selected' : '' }}>
                                                            Órgano de Alta Dirección
                                                        </option>
                                                        <option value="Órgano Especial"
                                                            {{ $entidad->tipo == 'Órgano Especial' ? 'selected' : '' }}>
                                                            Órgano Especial
                                                        </option>
                                                        <option value="Órgano de Asesoramiento"
                                                            {{ $entidad->tipo == 'Órgano de Asesoramiento' ? 'selected' : '' }}>
                                                            Órgano de Asesoramiento
                                                        </option>
                                                        <option value="Órgano de Apoyo"
                                                            {{ $entidad->tipo == 'Órgano de Apoyo' ? 'selected' : '' }}>
                                                            Órgano de Apoyo
                                                        </option>
                                                        <option value="Unidad"
                                                            {{ $entidad->tipo == 'Unidad' ? 'selected' : '' }}>
                                                            Unidad
                                                        </option>
                                                        <option value="Facultad"
                                                            {{ $entidad->tipo == 'Facultad' ? 'selected' : '' }}>
                                                            Facultad
                                                        </option>
                                                        <option value="Escuela Profesional"
                                                            {{ $entidad->tipo == 'Escuela Profesional' ? 'selected' : '' }}>
                                                            Escuela Profesional
                                                        </option>
                                                        <option value="Subentidad"
                                                            {{ $entidad->tipo == 'Subentidad' ? 'selected' : '' }}>
                                                            Subentidad
                                                        </option>
                                                        <option value="Unidad de Posgrado"
                                                            {{ $entidad->tipo == 'Unidad de Posgrado' ? 'selected' : '' }}>
                                                            Unidad de Posgrado
                                                        </option>
                                                        <option value="Combinado"
                                                            {{ $entidad->tipo == 'Combinado' ? 'selected' : '' }}>
                                                            Combinado
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="entidad_superior_id">Entidad Superior</label>
                                                    <select class="form-control" id="entidad_superior_id"
                                                        name="entidad_superior_id">
                                                        <option value="">Ninguna</option>
                                                        @foreach ($entidades as $superior)
                                                            <option value="{{ $superior->id }}"
                                                                {{ $entidad->entidad_superior_id == $superior->id ? 'selected' : '' }}>
                                                                {{ $superior->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
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
                            <!-- Fin Modal Editar -->
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No hay documentos registrados.</td>
                        </tr>
                    @endif
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
                {{ $entidades->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
        <!-- Modal de Confirmación de Eliminación -->
        @foreach ($entidades as $entidad)
            <div class="modal fade" id="deleteModal{{ $entidad->id }}" tabindex="-1"
                aria-labelledby="deleteModalLabel{{ $entidad->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $entidad->id }}">Confirmar
                                Eliminación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar este documento? Esta acción no se puede deshacer.
                        </div>
                        <div class="modal-footer">
                            <!-- Botón para cancelar -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            <!-- Formulario de eliminación -->
                            <form action="{{ route('entidades.destroy', $entidad->id) }}" method="POST"
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

    </div>

    <!-- Modal para crear -->
    <div class="modal fade" id="modalCrearEntidad" tabindex="-1" aria-labelledby="modalCrearEntidadLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearEntidadLabel">Nueva Entidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('entidades.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="siglas">Siglas</label>
                            <input type="text" class="form-control" id="siglas" name="siglas" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo" required>
                                <option value="Órgano de Alta Dirección">Órgano de Alta Dirección</option>
                                <option value="Órgano Especial">Órgano Especial</option>
                                <option value="Órgano de Asesoramiento">Órgano de Asesoramiento</option>
                                <option value="Órgano de Apoyo">Órgano de Apoyo</option>
                                <option value="Unidad">Unidad</option>
                                <option value="Facultad">Facultad</option>
                                <option value="Escuela Profesional">Escuela Profesional</option>
                                <option value="Subentidad">Subentidad</option>
                                <option value="Unidad de Posgrado">Unidad de Posgrado</option>
                                <option value="Combinado">Combinado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="entidad_superior_id">Entidad Superior</label>
                            <select class="form-control" id="entidad_superior_id" name="entidad_superior_id">
                                <option value="">Ninguna</option>
                                @foreach ($entidades as $superior)
                                    <option value="{{ $superior->id }}">{{ $superior->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Fin Modal Crear -->



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
