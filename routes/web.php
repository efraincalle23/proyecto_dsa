<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HistoricoController;

Route::get('/', function () {
    return view('auth.login');
});

Route::resource('usuarios', UsuarioController::class);

Route::resource('documentos', DocumentoController::class);

/*Route::get('documento', function () {
    return view('documentos/documentos');
});*/
Route::middleware(['auth'])->group(function () {
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
});
// Rutas tipo recurso para usuarios
Route::resource('users', UserController::class);

// Ruta para verificar el email (si es necesario)
Route::post('users/check-email', [UserController::class, 'checkEmail'])->name('users.checkEmail');

Route::get('/insertar-roles', [RolController::class, 'insertarRoles']);
Route::get('/usuarios/check-email', [UsuarioController::class, 'checkEmail'])->name('usuarios.checkEmail');


//login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Asegúrate de tener esta vista creada
    })->name('dashboard');
});

/*Route::get('documento', function () {
    return view('documentos/documentos');
});*/

Route::post('/historicos/{id}/asignar', [HistoricoController::class, 'asignar'])->name('historicos.asignar');
Route::get('/historicos', [HistoricoController::class, 'index'])->name('historicos.index');