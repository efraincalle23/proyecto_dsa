@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
    <p>Bienvenido al sistema de gestión de documentos del Proyecto DSA.</p>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Gestiona los usuarios del sistema.</p>
                    <a href="/usuarios" class="btn btn-light">Ver Usuarios</a>
                </div>
            </div>
        </div>
    </div>
@endsection
