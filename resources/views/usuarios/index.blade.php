<!-- resources/views/usuarios.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Gestión de Usuarios</h1>

        <!-- Botón para agregar un nuevo usuario -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Agregar Usuario</button>

        <!-- Tabla de usuarios -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->apellido }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->role->nombre_rol }}</td>
                        <td>
                            <!-- Botón para editar usuario -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $usuario->id_usuario }}" data-nombre="{{ $usuario->nombre }}"
                                data-apellido="{{ $usuario->apellido }}" data-email="{{ $usuario->email }}"
                                data-role="{{ $usuario->id_rol }}">Editar</button>

                            <!-- Botón para eliminar usuario -->
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $usuario->id_usuario }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal de Creación de Usuario -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('usuarios.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_rol" class="form-label">Rol</label>
                                <select name="id_rol" class="form-select" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_rol }}">{{ $role->nombre_rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Usuario -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" id="editForm" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="editNombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" name="apellido" id="editApellido" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_rol" class="form-label">Rol</label>
                                <select name="id_rol" id="editRole" class="form-select" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id_rol }}">{{ $role->nombre_rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación de Eliminación -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Eliminar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que quieres eliminar este usuario?
                    </div>
                    <div class="modal-footer">
                        <form method="POST" id="deleteForm" action="">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agregar scripts para editar y eliminar -->
    <script>
        // Llenar los campos del modal de edición
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var userId = button.data('id');
            var nombre = button.data('nombre');
            var apellido = button.data('apellido');
            var email = button.data('email');
            var role = button.data('role');

            var modal = $(this);
            modal.find('#editNombre').val(nombre);
            modal.find('#editApellido').val(apellido);
            modal.find('#editEmail').val(email);
            modal.find('#editRole').val(role);
            modal.find('#editForm').attr('action', '/usuarios/' + userId);
        });

        // Llenar el formulario de eliminación
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var userId = button.data('id');
            var modal = $(this);
            modal.find('#deleteForm').attr('action', '/usuarios/' + userId);
        });
    </script>
@endsection
