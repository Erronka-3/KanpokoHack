<?php
// Iniciar la sesión
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
session_start();
include '../menu/menu.php';
// Verificar si el usuario está autenticado
if (!isset($_SESSION['access_token'])) {
    die("<div class='alert alert-danger text-center'>No estás autenticado.</div>");
}
?>

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

</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container py-5">
        <!-- Título -->
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="h3">Gestión de Gastos</h1>
                <p class="lead">Registra y consulta los gastos de tu empresa.</p>
            </div>
        </div>

        <!-- Formulario para registrar un gasto -->
        <section class="row mb-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <h3 class="h5">Registrar Nuevo Gasto</h3>
                <form id="expense-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="expense-date">Fecha</label>
                        <input type="date" class="form-control" id="expense-date" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="expense-description">Descripción</label>
                        <input type="text" class="form-control" id="expense-description" name="descripcion"
                            placeholder="Descripción del gasto" required>
                    </div>
                    <div class="form-group">
                        <label for="expense-amount">Importe</label>
                        <input type="number" class="form-control" id="expense-amount" name="importe"
                            placeholder="Importe del gasto" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="expense-ticket">Adjuntar Ticket</label>
                        <input type="file" class="form-control-file" id="expense-ticket" name="ticket">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Registrar Gasto</button>
                </form>
            </div>
        </section>

        <!-- Historial de Gastos -->
        <section class="row">
            <div class="col-12">
                <h3 class="h5">Historial de Gastos</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody id="expenses-list">
                        <!-- Los gastos registrados se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
                <!-- Cálculo del Total -->
                <div class="text-right mt-3">
                    <h5>Total de Gastos: <span id="total-expenses">$0</span></h5>
                </div>
            </div>
        </section>
    </div>

    <!-- JS de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JS Personalizado -->
    <script>
    // Cargar los gastos desde el servidor
    function loadExpenses() {
        $.get('get_expenses.php', function(data) {
            let expenses = JSON.parse(data);
            let expensesList = $('#expenses-list');
            expensesList.empty(); // Limpiar la tabla antes de renderizar

            expenses.forEach(expense => {
                console.log(expense);
                expensesList.append(`
                        <tr>
                            <td>${expense.fecha}</td>
                            <td>${expense.descripcion}</td>
                            <td>$${expense.importe}</td>
                            <td><a href="storage/${expense.ticket}" class="btn btn-info btn-sm" target="_blank">Ver Ticket</a></td>
                            <td><button class="btn btn-danger btn-sm" onclick="deleteExpense(${expense.id})"><i class="fas fa-trash"></i></button></td>
                            </tr>
                    `);
            });

            // Actualizar el total
            let total = expenses.reduce((acc, expense) => acc + parseFloat(expense.importe), 0);
            $('#total-expenses').text('$' + total.toFixed(2));
        });
    }

    // Manejo del formulario de registro
    $('#expense-form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: 'register_expense.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Gasto registrado correctamente.');
                $('#expense-form')[0].reset(); // Limpiar el formulario
                loadExpenses(); // Recargar la tabla de gastos
            },
            error: function() {
                alert('Hubo un error al registrar el gasto.');
            }
        });
    });

    // Inicializar la lista de gastos
    $(document).ready(function() {
        loadExpenses();
    });

    // Función para eliminar un gasto
    function deleteExpense(expenseId) {
        if (confirm('¿Estás seguro de que deseas eliminar este gasto?')) {
            $.ajax({
                url: 'delete_expense.php',
                type: 'POST',
                data: {
                    id: expenseId
                },
                success: function(response) {
                    alert('Gasto eliminado correctamente.');
                    loadExpenses(); // Recargar la lista de gastos
                },
                error: function() {
                    alert('Hubo un error al eliminar el gasto.');
                }
            });
        }

        $('#expense-form').on('submit', function(e) {
            e.preventDefault();

            let importe = parseFloat($('#expense-amount').val());

            if (importe < 0) {
                alert('El importe no puede ser menor que 0.');
                return; // Evita que el formulario se envíe si la validación falla
            }

            // Si la validación pasa, envía el formulario
            let formData = new FormData(this);

            $.ajax({
                url: 'register_expense.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Gasto registrado correctamente.');
                    $('#expense-form')[0].reset(); // Limpiar el formulario
                },
                error: function() {
                    alert('Hubo un error al registrar el gasto.');
                }
            });
        });
    }
    </script>
</body>

</html>