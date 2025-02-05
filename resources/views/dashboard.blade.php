@extends('layouts.app')

<style>
    body {
        background-color: #f8f9fa;
    }

    .sidebar {
        background-color: #6c63ff;
        color: white;
        height: 100vh;
        padding: 1rem;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
    }

    .content-header {
        padding: 1rem;
    }

    .card-summary {
        border: none;
        border-radius: 12px;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        padding: 1rem;
    }

    .chart-container {
        background-color: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .recent-docs {
        background-color: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .recent-docs .doc-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }

    .recent-docs .doc-item:last-child {
        border-bottom: none;
    }
</style>
@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->

            <!-- Main Content -->
            <div class="col-md-12">
                <div class="content-header d-flex justify-content-between align-items-center">
                    <h2>Dashboard</h2>
                    <div>
                        <input type="text" class="form-control d-inline-block" style="width: 200px;"
                            placeholder="Buscar aquí">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
                <div class="row mb-4">
                    <!-- Summary Cards -->
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Documentos</h4>
                            <p>{{ $total_documentos }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Usuarios</h4>
                            <p>{{ $usuarios_count }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Pendientes</h4>
                            <p>{{ $pendientes_count }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Atendidos</h4>
                            <p>{{ $atendidos_count }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Chart -->
                    <div class="col-md-8">
                        <div class="chart-container">
                            <h5>Resumen mensual</h5>
                            <canvas id="chart"></canvas>
                        </div>
                    </div>
                    <!-- Recent Documents -->
                    <div class="col-md-4">
                        <div class="recent-docs">
                            <h5>Documentos recibidos</h5>
                            @foreach ($documentosRecibidos as $documento)
                                <div class="doc-item">
                                    <span>{{ $documento->nombre_doc }}</span>
                                    <span class="badge bg-success">{{ $documento->estado }}</span>
                                </div>
                            @endforeach

                            <div class="text-center mt-3">
                                <a href="/documentos_recibidos" class="btn btn-primary btn-sm">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($diasUltimos7Dias), // Las etiquetas con los últimos 7 días
                datasets: [{
                        label: 'Documentos Emitidos',
                        data: @json(array_values($documentosEmitidosData)), // Cantidad de documentos emitidos por día
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Documentos Recibidos',
                        data: @json(array_values($documentosRecibidosData)), // Cantidad de documentos recibidos por día
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
@endsection
