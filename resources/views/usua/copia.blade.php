@extends('layouts.app')

@section('content')
    <h1>Gestión de Usuarios</h1>

    <!-- Botón para abrir el modal de creación -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        Agregar Usuario
    </button>

    <!-- Tabla de usuarios -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->ID_Usuario }}</td>
                    <td>{{ $usuario->Nombre }}</td>
                    <td>{{ $usuario->Apellido }}</td>
                    <td>{{ $usuario->Email }}</td>
                    <td>
                        <!-- Botón de Editar -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editModal{{ $usuario->ID_Usuario }}">
                            Editar
                        </button>

                        <!-- Botón de Eliminar -->
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $usuario->ID_Usuario }}">
                            Eliminar
                        </button>
                    </td>
                </tr>

                <!-- Modal de Edición -->
                <div class="modal fade" id="editModal{{ $usuario->ID_Usuario }}" tabindex="-1"
                    aria-labelledby="editModalLabel{{ $usuario->ID_Usuario }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('usuarios.update', $usuario->ID_Usuario) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $usuario->ID_Usuario }}">Editar Usuario
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="Nombre{{ $usuario->ID_Usuario }}" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="Nombre{{ $usuario->ID_Usuario }}"
                                            name="Nombre" value="{{ $usuario->Nombre }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="Apellido{{ $usuario->ID_Usuario }}" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" id="Apellido{{ $usuario->ID_Usuario }}"
                                            name="Apellido" value="{{ $usuario->Apellido }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="Email{{ $usuario->ID_Usuario }}" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="Email{{ $usuario->ID_Usuario }}"
                                            name="Email" value="{{ $usuario->Email }}" required>
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

                <!-- Modal de Confirmación de Eliminación -->
                <div class="modal fade" id="deleteModal{{ $usuario->ID_Usuario }}" tabindex="-1"
                    aria-labelledby="deleteModalLabel{{ $usuario->ID_Usuario }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('usuarios.destroy', $usuario->ID_Usuario) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $usuario->ID_Usuario }}">Confirmar
                                        Eliminación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Estás seguro de que deseas eliminar este usuario?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
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
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="Nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="Apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="Apellido" name="Apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="Email" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="Contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="Contrasena" name="Contrasena" required>
                            <input type="file" class="form-control" id="Contrasena" name="Contrasena" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
