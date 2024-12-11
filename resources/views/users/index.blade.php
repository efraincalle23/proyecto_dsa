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
                        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
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
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-reset"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>

                        {{-- Tabla de usuarios --}}
                        <div class="container">
                            <div class="row">
                                @forelse($users as $user)
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <!-- Foto -->
                                                <div class="d-flex justify-content-center mb-3">
                                                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('path/to/default-avatar.png') }}"
                                                        alt="Foto" class="img-fluid rounded-circle" width="80"
                                                        height="80">
                                                </div>
                                                <!-- Nombre Completo -->
                                                <h5 class="card-title text-center">{{ $user->nombre }}
                                                    {{ $user->apellido }}</h5>
                                                <!-- Rol -->
                                                <p class="text-center">
                                                    <span class="badge bg-primary">{{ $user->rol }}</span>
                                                </p>
                                                <!-- Email -->
                                                <p class="text-center">{{ $user->email }}</p>
                                                <!-- Fecha de Creación -->
                                                <p class="text-center text-muted">
                                                    {{ $user->created_at->format('d/m/Y') }}</p>
                                                <!-- Acciones -->
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Botón de Editar -->
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editarUsuarioModal{{ $user->id }}"
                                                        data-id="{{ $user->id }}" data-nombre="{{ $user->nombre }}"
                                                        data-apellido="{{ $user->apellido }}"
                                                        data-email="{{ $user->email }}" data-rol="{{ $user->rol }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <!-- Botón de Eliminar -->
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $user->id }}">
                                                        <i class="bi bi-archive-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            No se encontraron usuarios.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>


                        {{-- Paginación --}}
                        <div class="d-flex justify-content-center">
                            {{ $users->appends(request()->input())->links() }}
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
                    <form id="formCrearUsuario" method="POST" action="{{ route('users.store') }}"
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
        @foreach ($users as $user)
            <div class="modal fade" id="editarUsuarioModal{{ $user->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('users.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <input type="hidden" name="id_usuario" value="{{ $user->id_user }}">
                                <div class="mb-3">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombre"
                                        value="{{ $user->nombre }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Apellido</label>
                                    <input type="text" class="form-control" name="apellido"
                                        value="{{ $user->apellido }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ $user->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Contraseña (opcional)</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Dejar en blanco si no desea cambiarla" minlength="8">
                                </div>
                                <div class="mb-3">
                                    <label>Rol</label>
                                    <select class="form-control" name="rol" required>
                                        <option value="Jefa DSA" {{ $user->rol == 'Jefa DSA' ? 'selected' : '' }}>Jefa
                                            DSA</option>
                                        <option value="Administrador"
                                            {{ $user->rol == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                        <option value="Administrativo"
                                            {{ $user->rol == 'Administrativo' ? 'selected' : '' }}>Administrativo
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
        @foreach ($users as $user)
            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
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
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
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