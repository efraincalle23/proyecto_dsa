<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <!-- Slider de imágenes de fondo -->
    <div class="background-slider">
        <div class="background-image image1 active"></div>
        <div class="background-image image2"></div>
        <div class="background-image image3"></div>
    </div>
    <div class="background-overlay"></div>


    <!-- Contenedor principal -->
    <div class="container-fluid login-container">
        <div class="card login-form shadow">
            <img src="{{ asset('assets/images/logotipo.png') }}" alt="Logo" class="logo">
            <h5 class="text-center  mb-3"><strong>DIRECCIÓN DE SERVICIOS ACADÉMICOS</strong></h5>

            <p class="text-center text-muted">Inicia sesión en tu cuenta</p>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </ul>

                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <!-- Nombre de Usuario -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Correo electrónico" required>
                    </div>
                </div>
                <!-- Contraseña -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Contraseña" required>
                    </div>
                </div>
                <!-- Acuérdate de mí -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Acuérdate de mí</label>
                    </div>
                    <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
                <!-- Botón de Inicio de Sesión -->
                <button type="submit" class="btn btn-primary w-100">Continuar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Script para cambiar imágenes -->

    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
