<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            min-height: 100vh;
        }

        .illustration {
            background-color: #6c63ff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .illustration img {
            max-width: 90%;
            height: auto;
        }

        .login-form {
            padding: 2rem;
        }

        .login-form .form-control {
            border-radius: 0.5rem;
        }

        .login-form .btn-primary {
            background-color: #6c63ff;
            border: none;
            border-radius: 0.5rem;
        }

        .login-form .btn-primary:hover {
            background-color: #574bdb;
        }

        .login-form .social-btn {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            background-color: #fff;
            color: #000;
        }

        .login-form .social-btn.google {
            color: #db4437;
        }

        .login-form .social-btn.google:hover {
            background-color: #f4d6d4;
        }
    </style>
</head>

<body>
    <div class="container-fluid login-container d-flex">
        <!-- Panel Izquierdo -->
        <div class="col-md-6 illustration d-none d-md-flex">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Ilustración">
        </div>
        <!-- Formulario -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="card login-form shadow-sm" style="width: 100%; max-width: 400px;">
                <h2 class="text-center">BIENVENIDO 👋</h2>
                <p class="text-center text-muted">Inicia sesión en tu cuenta</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
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

                <hr>
                <!-- Botones Sociales -->
                <a href="{{ route('auth.google') }}" class="btn btn-primary">
                    Iniciar sesión con Google
                </a>
                <button class="btn social-btn w-100 mb-2">
                    <i class="bi bi-envelope"></i> Iniciar sesión con Email
                </button>
                <button class="btn social-btn google w-100">
                    <i class="bi bi-google"></i> Iniciar sesión con Google
                </button>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>

</html>
