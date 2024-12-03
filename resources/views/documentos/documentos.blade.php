@extends('layouts.app')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Documento</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            body {
                background-color: #f2f6fc;
                /* Fondo azul claro */
            }

            .modal-header {
                background-color: #563d7c;
                /* Color morado */
                color: white;
            }

            .form-control {
                border-radius: 10px;
            }

            .modal-footer .btn {
                border-radius: 20px;
            }

            .drag-and-drop {
                border: 2px dashed #d6d6d6;
                border-radius: 10px;
                text-align: center;
                padding: 20px;
                color: #8c8c8c;
                font-size: 0.9rem;
            }
        </style>
    </head>

    <body>
        <div class="container py-5">
            <h1 class="mb-4">DOCUMENTOS</h1>

            <!-- Barra de búsqueda y botón -->
            <div class="d-flex justify-content-between mb-3">
                <input type="text" class="form-control w-50" placeholder="Busca aquí">
                <!-- Botón que abre el modal -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoDocumentoModal">+ Nuevo
                    documento</button>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th># Oficio</th>
                            <th>Fecha</th>
                            <th>Remitente</th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplo de filas -->
                        <tr>
                            <td>Documento 1</td>
                            <td>#1234</td>
                            <td>Nov 25, 2024</td>
                            <td>Maria William</td>
                            <td>Mariany</td>
                            <td>MAT</td>
                            <td>Descripción abc</td>
                            <td>
                                <!-- Botones de acción con iconos -->
                                <button class="btn btn-sm btn-warning btn-action"><i class="bi bi-pencil-fill"></i></button>
                                <button class="btn btn-sm btn-danger btn-action"><i class="bi bi-archive-fill"></i></button>
                                <button class="btn btn-sm btn-success btn-action"><i
                                        class="bi bi-cloud-arrow-down-fill"></i></button>
                            </td>
                        </tr>
                        <!-- Más filas aquí -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="nuevoDocumentoModal" tabindex="-1" aria-labelledby="nuevoDocumentoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoDocumentoModalLabel">NUEVO DOCUMENTO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="nuevoDocumentoForm">
                        <div class="modal-body">
                            <h6 class="text-uppercase fw-bold text-primary mb-3">Detalles del documento</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombreDocumento" class="form-label">Nombre del documento</label>
                                    <input type="text" class="form-control" id="nombreDocumento" name="nombreDocumento"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="numeroOficio" class="form-label">Número de oficio</label>
                                    <input type="text" class="form-control" id="numeroOficio" name="numeroOficio"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="remitente" class="form-label">Remitente</label>
                                    <input type="text" class="form-control" id="remitente" name="remitente" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fechaRecepcion" class="form-label">Fecha de recepción</label>
                                    <input type="date" class="form-control" id="fechaRecepcion" name="fechaRecepcion"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <input type="text" class="form-control" id="tipo" name="tipo" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="200"></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="documento" class="form-label">Documento</label>
                                    <div class="drag-and-drop">
                                        <label for="documento" class="form-label mb-0" style="cursor: pointer;">Arrastre y
                                            suelte o haga clic aquí para seleccionar el archivo</label>
                                        <input type="file" id="documento" name="documento" class="form-control"
                                            style="display: none;" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Subir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('nuevoDocumentoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Documento registrado exitosamente.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoDocumentoModal'));
                modal.hide();
                this.reset();
            });
        </script>
    </body>

    </html>
@endsection
