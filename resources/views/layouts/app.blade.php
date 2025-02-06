<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto DSA</title>

    <!-- Fuente Jost -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Agregar Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>

<body>
    <!-- Botón Toggle para móvil -->
    <button class="toggle-sidebar" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header text-center">
            <img src="{{ asset('assets/images/logotipo.png') }}" alt="Logo del Proyecto" class="img-fluid"
                style="max-width: 200px; display: block; margin: 0 auto;">
            <button class="btn btn-sm btn-link" id="collapseSidebar">
                <i class="bi bi-chevron-left"></i>
            </button>
            <!-- Botón para desplegar el sidebar (visible cuando se contrae) -->
            <button class="btn btn-sm btn-link" id="expandSidebar" style="display: none;">
                <i class="bi bi-chevron-right"></i> <!-- Ícono para desplegar -->
            </button>
        </div>
        <!-- Menú Original con Nuevos Estilos -->

        <a href="/dashboard"><i class="bi bi-house"></i> Dashboard</a>

        @if (Auth::user()->rol == 'Administrador')
            <a href="/documentos"><i class="bi bi-folder"></i> Documentos</a>
        @endif

        <!-- Submenú de Documentos -->
        <div class="sidebar-item">
            <a href="#" class="toggle-submenu" data-target="#submenu-documentos">
                <i class="bi bi-folder"></i> Documentos
                <i class="bi bi-chevron-down float-end ms-5"></i>
            </a>
            <div id="submenu-documentos" class="submenu">
                <a href="/documentos_recibidos"><i class="bi bi-arrow-down"></i> Recibidos</a>
                <a href="/documentos_emitidos"><i class="bi bi-arrow-up"></i> emitidos</a>
                <a href="/documentos-todos"><i class="bi bi-folder"></i> Todos</a>

                @if (Auth::user()->rol == 'Administrador')
                    <a href="/documentos/recibidos"><i class="bi bi-arrow-down"></i> Recibidos_1</a>
                    <a href="/documentos/emitidos"><i class="bi bi-arrow-up"></i> Emitidos_1</a>
                    <a href="/documentos"><i class="bi bi-folder"></i> Todos_1</a>
                @endif
            </div>
        </div>

        <a href="/entidades"><i class="bi bi-shield-lock"></i> Entidades</a>

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

    <!-- Contenido Principal -->
    <div class="content" id="mainContent">
        <div class="header">
            <h4 class="mb-0">Bienvenido(a) {{ Auth::user()->nombre ?? 'Usuario' }}</h4>
            <div class="d-flex align-items-center">
                <div class="icons dropdown">
                    <i class="bi bi-bell" id="dropdownMenuButton" data-bs-toggle="dropdown" title="Notificaciones"></i>
                    <span class="badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        @if (Auth::user()->unreadNotifications->isEmpty())
                            <li><a class="dropdown-item text-muted">No hay notificaciones</a></li>
                        @else
                            @foreach (Auth::user()->unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('notifications.show', $notification->id) }}">
                                        {{ $notification->data['message'] }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center text-muted" href="{{ route('notifications.index') }}">
                                Ver todas las notificaciones</a></li>
                    </ul>
                </div>
                <div class="user-text">
                    <p class="user-name">{{ Auth::user()->nombre . ' ' . Auth::user()->apellido ?? 'Usuario' }}</p>
                    <p class="user-role">{{ Auth::user()->rol ?? 'Rol no asignado' }}</p>
                </div>
                <div class="avatar ms-3">
                    <a href="{{ route('user.profile', Auth::user()->id) }}">
                        <img src="{{ Auth::user()->foto == 'assets/images/default.png' ? asset(Auth::user()->foto) : asset('storage/' . Auth::user()->foto) }}"
                            alt="Avatar">
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="table-responsive">

                @yield('content')
            </div>

        </div>
    </div>

    <!-- Botón de restauración del sidebar -->

    <!-- Footer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        // Control del Sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const collapseBtn = document.getElementById('collapseSidebar');
        const expandBtn = document.getElementById('expandSidebar');

        // Función para alternar entre colapsar y expandir el sidebar
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // Cambiar el ícono dependiendo del estado del sidebar
            const icon = collapseBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');

                // Mostrar el botón de expandir cuando el sidebar está colapsado
                expandBtn.style.display = 'block';
            } else {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');

                // Ocultar el botón de expandir cuando el sidebar está expandido
                expandBtn.style.display = 'none';
            }
        }

        // Asociar el evento al botón de colapso
        collapseBtn.addEventListener('click', toggleSidebar);

        // Asociar el evento al botón de expandir
        expandBtn.addEventListener('click', () => {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            collapseBtn.querySelector('i').classList.remove('bi-chevron-right');
            collapseBtn.querySelector('i').classList.add('bi-chevron-left');
            expandBtn.style.display = 'none'; // Ocultar el botón de expandir
        });

        // Cierre de sesión
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Cerrar sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, salir'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Seleccionamos todos los botones que activan un submenú
            document.querySelectorAll(".toggle-submenu").forEach(button => {
                button.addEventListener("click", function(event) {
                    event.preventDefault(); // Evita la navegación

                    let target = document.querySelector(this.getAttribute("data-target"));
                    if (target) {
                        // Alternar la visibilidad del submenú
                        target.style.display = (target.style.display === "block") ? "none" :
                            "block";

                        // Alternar la clase "aria-expanded" para cambiar el icono de flecha
                        this.setAttribute("aria-expanded", target.style.display === "block");
                    }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.querySelector(".sidebar");
            const toggleSidebar = document.querySelector(".toggle-sidebar");

            // Manejador de clic para mostrar/ocultar el sidebar
            toggleSidebar.addEventListener("click", function() {
                sidebar.classList.toggle("active");
            });

            // Si deseas que hacer clic fuera del sidebar lo cierre en dispositivos móviles:
            document.addEventListener("click", function(event) {
                if (!sidebar.contains(event.target) && !toggleSidebar.contains(event.target)) {
                    sidebar.classList.remove("active");
                }
            });
        });

        //colapsar
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const collapseBtn = document.getElementById("collapseSidebar");
            const expandBtn = document.getElementById("expandSidebar");

            // Evento para colapsar el sidebar
            collapseBtn.addEventListener("click", function() {
                sidebar.classList.add("collapsed");
                mainContent.classList.add("expanded");
                expandBtn.style.display = "block"; // Mostrar el botón de expandir
            });

            // Evento para expandir el sidebar
            expandBtn.addEventListener("click", function() {
                sidebar.classList.remove("collapsed");
                mainContent.classList.remove("expanded");
                expandBtn.style.display = "none"; // Ocultar el botón de expandir
            });
        });
    </script>
</body>


</html>
