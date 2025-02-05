@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">📩 Notificaciones</h4>
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-check2-all"></i> Marcar todas como leídas
                </button>
            </form>
        </div>

        @if ($notifications->isEmpty())
            <div class="alert alert-info text-center">
                <i class="bi bi-bell-slash"></i> No tienes notificaciones pendientes.
            </div>
        @else
            <div class="list-group">
                @foreach ($notifications as $notification)
                    <a href="{{ route('notifications.show', $notification->id) }}"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center 
                   {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">

                        <div>
                            <p class="mb-1">
                                <strong>{{ $notification->data['usuario'] }}</strong> te ha derivado el documento
                                <strong>#{{ $notification->data['nombre_documento'] }}</strong> para
                                <strong>{{ $notification->data['estado'] }}</strong>.
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </small>
                        </div>

                        @if (!$notification->read_at)
                            <span class="badge bg-danger rounded-pill">Nuevo</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <!-- Paginación -->
            <style>
                .card {
                    border-width: 2px;
                    border-radius: 8px;
                }

                .card-header {
                    font-size: 1rem;
                    font-weight: bold;
                }

                .card-footer {
                    font-size: 0.85rem;
                    background-color: #f8f9fa;
                }
            </style>
            <!-- Paginación -->


            <div class="mt-3 d-flex justify-content-center">
                {{ $notifications->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
