<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto DSA</title>

    <!-- Agregar Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

    <style>

    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            Proyecto DSA
        </div>
        @php
        $hasRole = auth()
        ->user()
        ->hasAnyRole(['Administrador', 'Jefe DSA']);
        @endphp
        @if ($hasRole)
        <a href="/dashboard"><i class="bi bi-house"></i> Dashboard</a>
        @endif
        @if (Auth::user()->rol == 'Administrador')
        <a href="/documentos"><i class="bi bi-folder"></i> Documentos</a>
        @endif
        <!-- Submenú de Documentos -->
        <div class="sidebar-item">
            <a href="#" class="toggle-submenu" data-target="#submenu-documentos">
                <i class="bi bi-folder"></i> Documentos
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <div id="submenu-documentos" class="submenu">
                <a href="/documentos_recibidos"><i class="bi bi-arrow-down"></i> Recibidos2</a>
                <a href="/documentos_emitidos"><i class="bi bi-arrow-up"></i> emitidos2</a>
                <a href="/documentos-todos"><i class="bi bi-folder"></i> Todos2</a>
                @if (Auth::user()->rol == 'Administrador')
                <a href="/documentos/recibidos"><i class="bi bi-arrow-down"></i> Recibidos</a>
                <a href="/documentos/emitidos"><i class="bi bi-arrow-up"></i> Emitidos</a>
                <a href="/documentos"><i class="bi bi-folder"></i> Todos</a>
                @endif
            </div>
        </div>
        @role('Administrador|Jefe DSA')
        @if (Auth::user()->rol == 'Administrador' || Auth::user()->rol == 'Jefe DSA')
        <a href="/users"><i class="bi bi-people"></i> Usuarios</a>
        <a href="/entidades"><i class="bi bi-shield-lock"></i> Entidades</a>
        @endif
        @if (Auth::user()->rol == 'Administrador')
        <a href="/historicos"><i class="bi bi-clock-history"></i> Históricos</a>
        <a href="/roles-permisos-test"><i class="bi bi-shield-lock"></i> Test permisos</a>
        <a href="#"><i class="bi bi-bell"></i> Notificaciones</a>
        <a href="#"><i class="bi bi-person-circle"></i> {{ Auth::user()->rol ?? 'Perfil' }}</a>
        @endif
        @endrole
        @role('Administrador|Jefe DSA')
        <a href="#"><i class="bi bi-person-circle"></i> {{ Auth::user()->rol ?? 'Perfil' }}</a>
        @endrole

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <a href="#" id="logout-link">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>

    </div>
    <!-- Main content -->
    <div class="content">
        <!-- Header -->
        <div class="header">
            <h4 class="mb-0">Bienvenido(a) {{ Auth::user()->nombre ?? 'Usuario' }}</h4>
            <div class="d-flex align-items-center">
                <!-- Iconos de notificaciones y configuración -->
                <div class="icons dropdown">
                    <i class="bi bi-bell" id="dropdownMenuButton" data-bs-toggle="dropdown" title="Notificaciones"></i>
                    <span class="badge">3</span>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#">Notificación 1</a></li>
                        <li><a class="dropdown-item" href="#">Notificación 2</a></li>
                        <li><a class="dropdown-item" href="#">Notificación 3</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center text-muted" href="#">Ver todas las
                                notificaciones</a></li>
                    </ul>
                </div>
                <!-- Nombre y Rol -->
                <div class="user-text">
                    <p class="user-name">{{ Auth::user()->nombre ?? 'Usuario' }}</p>
                    <p class="user-role">{{ Auth::user()->rol ?? 'Rol no asignado' }}</p>
                </div>
                <!-- Avatar de usuario -->

                <div class="avatar ms-3">
                    <a href="{{ route('user.profile', Auth::user()->id) }}">
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Avatar">
                    </a>
                </div>
            </div>
        </div>
        <div class="container mt-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/script.js') }}"></script>

</body>

</html>