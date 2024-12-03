<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('usuarios', UsuarioController::class);


Route::resource('documentos', DocumentoController::class);

Route::get('documento', function () {
    return view('documentos/documentos');
});