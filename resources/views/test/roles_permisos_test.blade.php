<!-- resources/views/roles/roles_permisos_test.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Prueba de Roles y Permisos</h1>

        <!-- Mostrar solo para Administradores y Jefa DSA -->
        @role('Administrador|Jefa DSA')
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Agregar Usuario</a>
        @endrole

        <!-- Mostrar solo si el usuario tiene el permiso 'eliminar usuarios' -->
        @can('eliminar usuarios')
            <h2>Usuarios</h2>
            <ul>
                @foreach ($usuarios as $usuario)
                    <li>
                        {{ $usuario->name }}
                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endcan

        <!-- Mostrar solo si el usuario tiene el permiso 'ver documentos' -->
        @can('ver documentos')
            <h2>Documentos</h2>
            <ul>
                @foreach ($documentos as $documento)
                    <li>
                        {{ $documento->numero_oficio }} - {{ $documento->descripcion }}
                    </li>
                @endforeach
            </ul>
        @endcan

        <!-- Mostrar solo para Administradores, Jefa DSA y Secretaria -->
        @role('Administrador|Jefa DSA|Secretaria')
            <a href="{{ route('documentos.create') }}" class="btn btn-primary">Agregar Documento</a>
        @endrole
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

@endsection
