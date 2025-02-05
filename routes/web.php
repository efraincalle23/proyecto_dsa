<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\DocumentoEmitidoController;
use App\Http\Controllers\HistorialDocumentoController;
use App\Http\Controllers\DocumentoRecibidoController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\DocumentosTodosController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('auth.login');
});

Route::resource('usuarios', UsuarioController::class);

Route::resource('documentos', DocumentoController::class)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
    'destroy'
]);
/*Route::get('documento', function () {
    return view('documentos/documentos');
});*/
Route::middleware(['auth'])->group(function () {
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
});




// Rutas tipo recurso para usuarios

Route::resource('users', controller: UserController::class)->middleware('auth');


Route::put('/users/{user}/updateFoto', [UserController::class, 'updateFoto'])->name('users.updateFoto')->middleware('auth');


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

//Route::post('/historicos/{id}/asignar', [HistoricoController::class, 'asignar'])->name('historicos.asignar');
Route::get('/historicos', [HistoricoController::class, 'index'])->name('historicos.index');

Route::post('/historicos/asignar/{id_documento}', [HistoricoController::class, 'asignar'])->name(name: 'historicos.asignar');
Route::post('/historicos/asignar2/{id_documento}', [HistoricoController::class, 'asignar2'])->name(name: 'historicos.asignar2');


use App\Http\Controllers\RolesPermisosTestController;
Route::get('/roles-permisos-test', [RolesPermisosTestController::class, 'index'])->name('roles-permisos-test');
use App\Http\Controllers\Auth\SocialController;

Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('/perfil/{id}', [UserController::class, 'show'])->name('user.profile')->middleware('auth');
Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword')->middleware('auth');



Route::get('/documentos/recibidos', [DocumentoController::class, 'recibidos'])->name('documentos.recibidos');
Route::get('/documentos/emitidos', action: [DocumentoController::class, 'emitidos'])->name('documentos.emitidos');

Route::post('/documentos/recibido', [DocumentoController::class, 'storeRecibido'])->name('documentos.storeRecibido');
Route::post('/documentos/emitido', [DocumentoController::class, 'storeEmitido'])->name('documentos.storeEmitido');

Route::post('/documentos/respuesta', [DocumentoController::class, 'storeRespuesta'])->name('documentos.storeRespuesta');

Route::get('/documentos/emitidoNro', action: [DocumentoController::class, 'emitidoNro'])->name('documentos.emitidoNro');
Route::get('/documentos/recibidoNro', action: [DocumentoController::class, 'recibidoNro'])->name('documentos.recibidos');




Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');



Route::resource('entidades', EntidadController::class)->middleware('auth');

Route::put('/entidades/{entidad}', [EntidadController::class, 'update'])->name('entidades.update')->middleware('auth');
Route::delete('entidades/{entidad}', [EntidadController::class, 'destroy'])->name('entidades.destroy')->middleware('auth');


Route::resource('documentos_recibidos', controller: DocumentoRecibidoController::class)->middleware('auth');
Route::put('/documentos/{id}/restore', [DocumentoRecibidoController::class, 'restore'])->name('documentos.restore')->middleware('auth');



Route::post('/historial/asignar/{id_documento}', [HistorialDocumentoController::class, 'asignar'])->name('historial.asignar')->middleware('auth');
Route::post('/historial/asignarEmitidos/{id_documento}', [HistorialDocumentoController::class, 'asignarEmitidos'])->name('historial.asignarEmitidos')->middleware('auth');

Route::resource('documentos_emitidos', DocumentoEmitidoController::class)->middleware('auth');
;
Route::put('/documentos_emitidos/{id}/restore', [DocumentoEmitidoController::class, 'restore'])->name('documentos_emitidos.restore')->middleware('auth');
Route::put('/documentos/emitidos/{documento}/respuesta', [DocumentoEmitidoController::class, 'storeRespuesta'])->name('documentos_emitidos.storeRespuesta')->middleware('auth');


Route::put('/documentos/recibidos/{documento}/respuesta', [DocumentoRecibidoController::class, 'storeRespuestaRecibida'])->name('documentos_recibidos.storeRespuestaRecibida')->middleware('auth');

Route::get('/documentos-todos', [DocumentosTodosController::class, 'index'])->name('documentos_todos.index')->middleware('auth');


// En routes/web.php
Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index')->middleware('auth');
Route::get('/notificaciones/{notification}', [NotificationController::class, 'show'])->name('notifications.show');

// Ruta para marcar todas las notificaciones como leídas
Route::post('/notificaciones/marcar-todas', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead')->middleware('auth');

Route::get('/dashboard', [DocumentosTodosController::class, 'contarDocumentos'])->middleware('auth');
;