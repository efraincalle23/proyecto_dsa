<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('ruta-a-tu-imagen-de-fondo.jpg');
            /* Reemplaza con tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            margin: auto;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .btn-google {
            background-color: #db4437;
            color: white;
            border: none;
        }

        .btn-google:hover {
            background-color: #c33c2f;
        }

        .text-muted {
            font-size: 0.9rem;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
        }

        .alert {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card text-center">
            <img src="{{ asset('assets/images/logotipo.png') }}" alt="Logo" class="logo">
            <!-- Reemplaza con tu logo -->
            <h2 class="form-group">UNPRG</h2>
            <h5 class="form-group">DIRECCION DE SERVICIOS ACADÉMICOS</h5>
            <p class="text-muted">Si tienes problemas para iniciar sesión, por favor contacta al administrador del
                sistema.</p>

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
                <!-- Email -->
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Ingresa tu correo" required>
                </div>
                <!-- Password -->
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Ingresa tu contraseña" required>
                </div>
                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
            </form>


        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
