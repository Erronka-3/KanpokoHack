<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gastos</title>
    <!-- Incluye Bootstrap desde un CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../app/assets/css/styles_expenses.css">
    <script defer src="../app/assets/js/scripts_expenses.js"></script>
</head>
<?php
// Iniciar la sesión
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
session_start();


// Verificar si el usuario está autenticado
if (!isset($_SESSION['access_token'])) {
    // die("<div class='alert alert-danger text-center'>No estás autenticado.</div>");
    header("Location: 'index.php?route=6");
    exit;
}

include '../app/pages/menu/menu.php';
?>



<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container py-5">
        <!-- Título -->
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="h3">Gestión de Gastos</h1>
                <p class="lead">Registra y consulta los gastos de tu empresa.</p>
            </div>
        </div>


        <!-- Botón para abrir el formulario -->
        <div class="row mb-3 justify-content-center">
            <div class="col-12 col-md-6 mt-3">
                <button id="open-create-form-btn" class="btn btn-primary w-100">Agregar Nuevo Registro</button>
            </div>
        </div>

        <!-- Filtro para seleccionar un gasto a actualizar -->
        <div class="row mb-3 justify-content-center">
            <div class="col-12 col-md-6">
                <label for="select-expense">Selecciona un Gasto para Actualizar</label>
                <select id="select-expense" class="form-control">
                    <option value="">Selecciona un gasto...</option>
                    <!-- Las opciones se cargarán dinámicamente aquí -->
                </select>
            </div>
        </div>

        <!-- Formulario para Crear un Gasto -->
        <div id="create-form-container" class="expense-form-container">
            <div class="form-overlay">
                <h3 class="h5">Registrar Nuevo Gasto</h3>
                <form id="create-expense-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="create-expense-date">Fecha</label>
                        <input type="date" class="form-control" id="create-expense-date" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="create-expense-description">Descripción</label>
                        <input type="text" class="form-control" id="create-expense-description" name="descripcion"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="create-expense-amount">Importe</label>
                        <input type="number" class="form-control" id="create-expense-amount" name="importe" required>
                    </div>
                    <div class="form-group">
                        <label for="create-expense-ticket">Adjuntar Ticket</label>
                        <input type="file" class="form-control-file" id="create-expense-ticket" name="ticket" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar Gasto</button>
                </form>
                <span id="close-create-form-btn">&times;</span>
            </div>
        </div>

        <!-- Formulario para Actualizar un Gasto -->
        <div id="update-form-container" class="expense-form-container">
            <div class="form-overlay">
                <h3 class="h5">Actualizar Gasto</h3>
                <form id="update-expense-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="update-expense-id" name="id">
                    <div class="form-group">
                        <label for="update-expense-date">Fecha</label>
                        <input type="date" class="form-control" id="update-expense-date" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="update-expense-description">Descripción</label>
                        <input type="text" class="form-control" id="update-expense-description" name="descripcion"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="update-expense-amount">Importe</label>
                        <input type="number" class="form-control" id="update-expense-amount" name="importe" required>
                    </div>
                    <div class="form-group">
                        <label for="update-expense-ticket">Adjuntar Ticket</label>
                        <input type="file" class="form-control-file" id="update-expense-ticket" name="ticket">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Actualizar Gasto</button>
                </form>
                <span id="close-update-form-btn">&times;</span>
            </div>
        </div>

        <!-- Filtro de fechas y rango de importes -->
        <div class="row mb-3 justify-content-center">
            <!-- Columna para Fecha Inicio -->
            <div class="col-12 col-md-6">
                <label for="expense-start-date">Fecha Inicio</label>
                <input type="date" class="form-control" id="expense-start-date">
            </div>
        </div>

        <div class="row mb-3 justify-content-center">
            <!-- Columna para Fecha Fin debajo de Fecha Inicio -->
            <div class="col-12 col-md-6">
                <label for="expense-end-date">Fecha Fin</label>
                <input type="date" class="form-control" id="expense-end-date">
            </div>
        </div>

        <div class="row mb-3 justify-content-center">
            <!-- Columna para Importe Mínimo alineado a la derecha -->
            <div class="col-12 col-md-3">
                <label for="expense-min-amount">Importe Mínimo (€)</label>
                <input type="number" class="form-control" id="expense-min-amount" placeholder="Importe mínimo">
            </div>

            <!-- Columna para Importe Máximo alineado a la derecha -->
            <div class="col-12 col-md-3">
                <label for="expense-max-amount">Importe Máximo (€)</label>
                <input type="number" class="form-control" id="expense-max-amount" placeholder="Importe máximo">
            </div>
        </div>



        <!-- Historial de Gastos -->
        <section class="row">
            <div class="col-12">
                <h3 class="h5">Historial de Gastos</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha
                                <button id="sort-date" class="btn btn-sm">
                                    <i class="fas fa-arrow-down text-white"></i> <!-- Flecha hacia abajo -->
                                </button>
                            </th>
                            <th>Descripción</th>
                            <th>Importe
                                <button id="sort-amount" class="btn btn-sm">
                                    <i class="fas fa-arrow-down text-white"></i> <!-- Flecha hacia abajo -->
                                </button>
                            </th>
                            <th>Ticket</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="expenses-list">
                        <!-- Los gastos registrados se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
                <!-- Cálculo del Total -->
                <div class="text-right mt-3">
                    <h5>Total de Gastos: <span id="total-expenses">0€</span></h5>
                </div>
            </div>
        </section>
    </div>

    <!-- JS de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Agregar SortableJS desde un CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


</body>

</html>