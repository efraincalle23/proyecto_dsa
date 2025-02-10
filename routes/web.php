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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\DocumentosExport;
use App\Exports\DocumentosRecibidosExport;
use App\Exports\DocumentosEmitidosExport;
use App\Exports\HistorialDocumentosExport;
use App\Http\Controllers\ConfiguracionController;
// Redirigir la raíz según autenticación
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});


// Ruta para login
Route::get('/login', function () {
    return view('auth.login'); // Ajusta esto según tu vista de login
})->name('login');

/*Route::get('documento', function () {
    return view('documentos/documentos');
});*/


//para controlar usuarios:
Route::get('/users/monitoreo', function () {
    $usuarios = DB::table('user_sessions')
        ->join('users', 'user_sessions.user_id', '=', 'users.id')
        ->select('users.id', 'users.nombre', 'users.apellido', 'user_sessions.ip_address', 'user_sessions.last_activity', 'user_sessions.is_active')
        ->get();

    return view('users.usuarios_monitoreo', compact('usuarios'));
})->middleware('auth');


Route::post('/users/desconectar/{id}', function ($id) {
    // Desactivar al usuario en la base de datos
    DB::table('user_sessions')->where('user_id', $id)->update(['is_active' => false]);

    // Eliminar la sesión del usuario
    DB::table('sessions')->where('user_id', $id)->delete();

    return redirect()->back()->with('success', 'Usuario desconectado');
})->name('users.desconectar');

//para exportar documentos todos
Route::get('/exportar-documentos', function () {
    return (new DocumentosExport())->export();
})->middleware('auth');

Route::get('/exportar-recibidos', function () {
    return (new DocumentosRecibidosExport())->export();
})->middleware('auth');

Route::get('/exportar-emitidos', function () {
    return (new DocumentosEmitidosExport())->export();
})->middleware('auth');

Route::get('/exportar-historial', function () {
    return (new HistorialDocumentosExport())->export();
})->middleware('auth');


//definir nuevo inicio

Route::post('/configuracion/numero-oficio-inicio', [ConfiguracionController::class, 'actualizarNumeroOficioInicio'])->name('configuracion.actualizarNumeroOficioInicio');



// Rutas tipo recurso para usuarios

Route::resource('users', controller: UserController::class)->middleware('auth');


Route::put('/users/{user}/updateFoto', [UserController::class, 'updateFoto'])->name('users.updateFoto')->middleware('auth');


// Ruta para verificar el email (si es necesario)
Route::post('users/check-email', [UserController::class, 'checkEmail'])->name('users.checkEmail');

Route::get('/insertar-roles', [RolController::class, 'insertarRoles']);


//login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Asegúrate de tener esta vista creada
    })->name('dashboard');
});



use App\Http\Controllers\RolesPermisosTestController;
Route::get('/roles-permisos-test', [RolesPermisosTestController::class, 'index'])->name('roles-permisos-test');
use App\Http\Controllers\Auth\SocialController;

Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('/perfil/{id}', [UserController::class, 'show'])->name('user.profile')->middleware('auth');
Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword')->middleware('auth');



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

Route::get('/historial', [HistorialDocumentoController::class, 'index'])->name('historial.index')->middleware('auth');

Route::delete('/documentos-emitidos/{id}/force-delete', [DocumentoEmitidoController::class, 'forceDelete'])->name('documentos_emitidos.forceDelete')->middleware('auth');

Route::delete('/documentos-recibidos/{id}/force-delete', [DocumentoRecibidoController::class, 'forceDelete'])->name('documentos_recibidos.forceDelete')->middleware('auth');
;