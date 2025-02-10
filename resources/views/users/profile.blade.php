@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-secondarytext-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>Perfil de Usuario
                    </h5>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">
                        <i class="bi bi-pencil-square me-1"></i>Editar Perfil
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Avatar y Datos -->
                    <div class="col-md-4 text-center">
                        <img src="{{ Auth::user()->foto == 'assets/images/default.png' ? asset(Auth::user()->foto) : asset('storage/' . Auth::user()->foto) }}"
                            class="rounded-circle img-thumbnail mb-3" alt="Avatar" style="width: 150px; height: 150px;">
                        <h5 class="text-primary mb-0 fw-bold">{{ $user->nombre }} {{ $user->apellido }}</h5>
                        <p class="text-muted mt-0 mb-0">{{ $user->rol }}</p>

                    </div>
                    <!-- Información -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="fw-bold"><i class="bi bi-envelope me-2"></i>Email:</label>
                            <p class="ms-4">{{ $user->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold"><i class="bi bi-telephone me-2"></i>Teléfono:</label>
                            <p class="ms-4">{{ $user->telefono ?? 'No registrado' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold"><i class="bi bi-geo-alt me-2"></i>Ubicación:</label>
                            <p class="ms-4">UNPRG</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de cambio de contraseña -->
                <div class="mt-4">
                    <h5 class="text-dark"><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                    <form action="{{ route('users.updatePassword', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                    placeholder="Ingrese su contraseña actual" required>
                            </div>
                            <div class="col-md-4">
                                <label for="new_password">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    placeholder="Nueva contraseña" minlength="8" required>
                            </div>
                            <div class="col-md-4">
                                <label for="new_password_confirmation">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="new_password_confirmation"
                                    name="new_password_confirmation" placeholder="Confirmar contraseña" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary mt-3">
                            <i class="bi bi-check-circle me-2"></i>Guardar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.updateFoto', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" value="{{ $user->nombre }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Apellido</label>
                            <input type="text" class="form-control" name="apellido" value="{{ $user->apellido }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        @if (Auth::user()->rol == 'Administrador' || Auth::user()->rol == 'Jefe DSA')
                            <div class="mb-3">
                                <label>Rol</label>
                                <select class="form-select" name="rol" required>
                                    <option value="Jefe DSA" {{ $user->rol == 'Jefe DSA' ? 'selected' : '' }}>Jefe DSA
                                    </option>
                                    <option value="Administrador" {{ $user->rol == 'Administrador' ? 'selected' : '' }}>
                                        Administrador</option>
                                    <option value="Administrativo" {{ $user->rol == 'Administrativo' ? 'selected' : '' }}>
                                        Administrativo</option>
                                    <option value="Secretaria" {{ $user->rol == 'Secretaria' ? 'selected' : '' }}>
                                        Secretaria
                                    </option>
                                </select>
                            </div>
                        @else
                            <div class="mb-3">
                                <label hidden>Rol</label>
                                <select class="form-select" name="rol" required hidden>
                                    <option value="Jefe DSA" {{ $user->rol == 'Jefe DSA' ? 'selected' : '' }}>Jefe DSA
                                    </option>
                                    <option value="Administrador" {{ $user->rol == 'Administrador' ? 'selected' : '' }}>
                                        Administrador</option>
                                    <option value="Administrativo" {{ $user->rol == 'Administrativo' ? 'selected' : '' }}>
                                        Administrativo</option>
                                    <option value="Secretaria" {{ $user->rol == 'Secretaria' ? 'selected' : '' }}>
                                        Secretaria
                                    </option>
                                </select>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label>Foto</label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert para errores y éxitos -->
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: "{{ implode(', ', $errors->all()) }}",
                    timer: 4000,
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
