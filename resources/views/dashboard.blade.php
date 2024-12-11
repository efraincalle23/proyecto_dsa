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
                            <p>186</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Usuarios</h4>
                            <p>9</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Pendientes</h4>
                            <p>80</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-summary">
                            <h4>Atendidos</h4>
                            <p>106</p>
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
                            <h5>Documentos recientes</h5>
                            <div class="doc-item">
                                <span>Oficio 1401</span>
                                <span class="badge bg-success">Atendido</span>
                            </div>
                            <div class="doc-item">
                                <span>Oficio 1402</span>
                                <span class="badge bg-warning">Para firma</span>
                            </div>
                            <div class="doc-item">
                                <span>Oficio 1403</span>
                                <span class="badge bg-success">Atendido</span>
                            </div>
                            <div class="doc-item">
                                <span>Oficio 1404</span>
                                <span class="badge bg-danger">Observado</span>
                            </div>
                            <div class="doc-item">
                                <span>Oficio 1405</span>
                                <span class="badge bg-success">Atendido</span>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-primary btn-sm">Ver más</button>
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
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                        label: 'Este mes',
                        data: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120],
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    },
                    {
                        label: 'El mes pasado',
                        data: [15, 25, 35, 45, 55, 65, 75, 85, 95, 105, 115, 125],
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
