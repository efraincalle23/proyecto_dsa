@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Gestión de Usuarios</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#crearUsuarioModal">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </button>
                    </div>
                    <div class="card-body">
                        {{-- Filtros de búsqueda --}}
                        <form method="GET" action="{{ route('usuarios.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="nombre" class="form-control"
                                        placeholder="Buscar por nombre" value="{{ request('nombre') }}">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="email" class="form-control" placeholder="Buscar por email"
                                        value="{{ request('email') }}">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="rol" required>
                                        <option value="Jefa DSA">Jefa DSA</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Administrativo">Administrativo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-secondary me-2">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-reset"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>

                        {{-- Tabla de usuarios --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Foto</th>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuarios as $usuario)
                                        <tr>
                                            <td>
                                                <img src="{{ $usuario->foto ? asset('storage/' . $usuario->foto) : asset('path/to/default-avatar.png') }}"
                                                    alt="Foto" class="img-fluid rounded-circle" width="40"
                                                    height="40">
                                            </td>
                                            <td>{{ $usuario->id_usuario }}</td>
                                            <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $usuario->rol }}
                                                </span>
                                            </td>
                                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Botón de Editar -->
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editarUsuarioModal{{ $usuario->id_usuario }}"
                                                        data-id="{{ $usuario->id_usuario }}"
                                                        data-nombre="{{ $usuario->nombre }}"
                                                        data-apellido="{{ $usuario->apellido }}"
                                                        data-email="{{ $usuario->email }}"
                                                        data-password="{{ $usuario->email }}"
                                                        data-rol="{{ $usuario->rol }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <!-- Botón de Eliminar que activa el modal -->
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $usuario->id_usuario }}">
                                                        <i class="bi bi-archive-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <div class="alert alert-info">
                                                    No se encontraron usuarios.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="d-flex justify-content-center">
                            {{ $usuarios->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Crear Usuario --}}
        <div class="modal fade" id="crearUsuarioModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Crear Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearUsuario" method="POST" action="{{ route('usuarios.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label>Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label>Contraseña</label>
                                <input type="password" class="form-control" name="password" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label>Rol</label>
                                <select class="form-control" name="rol" required>
                                    <option value="Jefa DSA">Jefa DSA</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Administrativo">Administrativo</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Foto (Opcional)</label>
                                <input type="file" class="form-control" name="foto" accept="image/*">
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

        {{-- Modal Editar Usuario --}}
        @foreach ($usuarios as $usuario)
            <div class="modal fade" id="editarUsuarioModal{{ $usuario->id_usuario }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <input type="hidden" name="id_usuario" value="{{ $usuario->id_usuario }}">
                                <div class="mb-3">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombre"
                                        value="{{ $usuario->nombre }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Apellido</label>
                                    <input type="text" class="form-control" name="apellido"
                                        value="{{ $usuario->apellido }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ $usuario->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Contraseña (opcional)</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Dejar en blanco si no desea cambiarla" minlength="8">
                                </div>
                                <div class="mb-3">
                                    <label>Rol</label>
                                    <select class="form-control" name="rol" required>
                                        <option value="Jefa DSA" {{ $usuario->rol == 'Jefa DSA' ? 'selected' : '' }}>Jefa
                                            DSA</option>
                                        <option value="Administrador"
                                            {{ $usuario->rol == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                        <option value="Administrativo"
                                            {{ $usuario->rol == 'Administrativo' ? 'selected' : '' }}>Administrativo
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Foto</label>
                                    <input type="file" class="form-control" name="foto" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Modal Eliminar Usuario --}}
        @foreach ($usuarios as $usuario)
            <div class="modal fade" id="deleteModal{{ $usuario->id_usuario }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Eliminar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar a este usuario?
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

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
