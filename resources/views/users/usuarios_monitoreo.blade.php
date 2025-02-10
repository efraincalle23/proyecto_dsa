@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-white p-4 rounded shadow">
        <h2>Monitoreo de Usuarios</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>IP</th>
                        <th>Última Actividad</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                            <td>{{ $usuario->ip_address }}</td>
                            <td>{{ $usuario->last_activity }}</td>
                            <td>
                                @if ($usuario->is_active)
                                    <button type="button" class="btn btn-outline-success btn-sm">Activo</button>
                                @else
                                    <button type="button" class="btn btn-outline-danger btn-sm">Inactivo</button>
                                @endif
                            </td>
                            <td>
                                @if ($usuario->is_active)
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmarDesconexion({{ $usuario->id }})">Quitar Acceso</button>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Desconectado</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setInterval(function() {
            $.get("{{ url('/users/monitoreo') }}", function(data) {
                $("tbody").html($(data).find("tbody").html());
            });
        }, 5000); // Actualizar cada 5 segundos
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarDesconexion(userId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción desconectará al usuario.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, quitar acceso',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('/users/desconectar') }}/" + userId, {
                        _token: "{{ csrf_token() }}"
                    }, function(response) {
                        Swal.fire('¡Desconectado!', 'El usuario ha sido desconectado.', 'success');
                        setTimeout(() => location.reload(), 2000);
                    }).fail(function() {
                        Swal.fire('¡Error!', 'No se pudo desconectar al usuario.', 'error');
                    });
                }
            });
        }
    </script>
    <!-- Alertas -->
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
