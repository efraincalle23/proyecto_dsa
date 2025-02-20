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
                            <option value="Oficina" {{ request('tipo') == 'Oficina' ? 'selected' : '' }}>Oficina</option>
                            <option value="Escuela" {{ request('tipo') == 'Escuela' ? 'selected' : '' }}>Escuela</option>
                            <option value="Dirección" {{ request('tipo') == 'Dirección' ? 'selected' : '' }}>Dirección
                            </option>
                            <option value="Unidad" {{ request('tipo') == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                            <option value="Escuela Profesional"
                                {{ request('tipo') == 'Escuela Profesional' ? 'selected' : '' }}>Escuela Profesional
                            </option>
                            <option value="Unidad de Posgrado"
                                {{ request('tipo') == 'Unidad de Posgrado' ? 'selected' : '' }}>Unidad de Posgrado</option>
                            <option value="Decanato" {{ request('tipo') == 'Decanato' ? 'selected' : '' }}>Decanato
                            </option>
                            <option value="Departamento" {{ request('tipo') == 'Departamento' ? 'selected' : '' }}>
                                Departamento</option>
                            <option value="Circular" {{ request('tipo') == 'Circular' ? 'selected' : '' }}>Circular
                            </option>
                            <option value="Otro" {{ request('tipo') == 'Otro' ? 'selected' : '' }}>Otro</option>

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
                    @if ($entidadesPaginadas->isNotEmpty())
                        @foreach ($entidadesPaginadas as $entidad)
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
                                                        <option value="Oficina"
                                                            {{ $entidad->tipo == 'Oficina' ? 'selected' : '' }}>
                                                            Oficina
                                                        </option>
                                                        <option value="Escuela"
                                                            {{ $entidad->tipo == 'Escuela' ? 'selected' : '' }}>
                                                            Escuela
                                                        </option>
                                                        <option value="Dirección"
                                                            {{ $entidad->tipo == 'Dirección' ? 'selected' : '' }}>
                                                            Dirección
                                                        </option>
                                                        <option value="Unidad"
                                                            {{ $entidad->tipo == 'Unidad' ? 'selected' : '' }}>
                                                            Unidad
                                                        </option>
                                                        <option value="Escuela Profesional"
                                                            {{ $entidad->tipo == 'Escuela Profesional' ? 'selected' : '' }}>
                                                            Escuela Profesional
                                                        </option>
                                                        <option value="Unidad de Posgrado"
                                                            {{ $entidad->tipo == 'Unidad de Posgrado' ? 'selected' : '' }}>
                                                            Unidad de Posgrado
                                                        </option>
                                                        <option value="Decanato"
                                                            {{ $entidad->tipo == 'Decanato' ? 'selected' : '' }}>
                                                            Decanato
                                                        </option>
                                                        <option value="Departamento"
                                                            {{ $entidad->tipo == 'Departamento' ? 'selected' : '' }}>
                                                            Departamento
                                                        </option>
                                                        <option value="Circular"
                                                            {{ $entidad->tipo == 'Circular' ? 'selected' : '' }}>
                                                            Circular
                                                        </option>
                                                        <option value="Otro"
                                                            {{ $entidad->tipo == 'Otro' ? 'selected' : '' }}>
                                                            Circular
                                                        </option>
                                                    </select>

                                                </div>
                                                <div class="form-group">
                                                    <label for="entidad_superior_id">Entidad Superior</label>
                                                    <select class="js-example-basic-single" id="entidad_superior_id"
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
                {{ $entidadesPaginadas->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
        <!-- Modal de Confirmación de Eliminación -->
        @foreach ($entidadesPaginadas as $entidad)
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
                                <option value="Oficina">Oficina</option>
                                <option value="Escuela">Escuela</option>
                                <option value="Dirección">Dirección</option>
                                <option value="Unidad">Unidad</option>
                                <option value="Escuela Profesional">Escuela Profesional</option>
                                <option value="Unidad de Posgrado">Unidad de Posgrado</option>
                                <option value="Decanato">Decanato</option>
                                <option value="Departamento">Departamento</option>
                                <option value="Circular">Circular</option>
                                <option value="Otro">Otro</option>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="entidad_superior_id">Entidad Superior</label>
                            <select class="js-example-basic-single" id="entidad_superior_id" name="entidad_superior_id">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Función para inicializar Select2 con opciones comunes
            function initSelect2($select, extraOptions = {}) {
                $select.select2($.extend({
                    width: '100%', // Ocupa el 100% del contenedor
                    placeholder: 'Seleccione una opción', // Texto de placeholder (opcional)
                    allowClear: true, // Permite borrar la selección
                    language: "es" // Configura el idioma a español
                }, extraOptions));
            }

            // Inicializar todos los select con la clase .js-example-basic-single
            $('.js-example-basic-single').each(function() {
                var $select = $(this);
                // Si el select está dentro de un modal, establecemos dropdownParent
                var $modal = $select.closest('.modal');
                if ($modal.length) {
                    initSelect2($select, {
                        dropdownParent: $modal
                    });
                } else {
                    initSelect2($select);
                }
            });

            // Si los modales se abren dinámicamente, asegúrate de re-inicializar los select dentro del modal al mostrarse.
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.js-example-basic-single').each(function() {
                    var $select = $(this);
                    // Si ya está inicializado, no se vuelve a inicializar.
                    if (!$select.data('select2')) {
                        initSelect2($select, {
                            dropdownParent: $(this).closest('.modal')
                        });
                    }
                });
            });
        });
    </script>


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
