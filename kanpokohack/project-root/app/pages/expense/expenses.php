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
    <style>
    /* Estilo para el fondo semitransparente */
    .expense-form-container {
        display: none;
        /* Inicialmente oculto */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        /* Fondo negro semitransparente */
        z-index: 999;
        /* Asegurarse de que el fondo cubra el contenido */
        align-items: center;
        justify-content: center;
    }

    /* Estilo para el contenedor del formulario */
    .form-overlay {
        background-color: white;
        padding: 20px;
        /* Aumenta el padding para dar más espacio al formulario */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 800px;
        /* Aumenta el tamaño máximo del formulario */
        position: relative;
        box-sizing: border-box;
        /* Asegura que el padding no afecte el ancho total */
    }

    /* Estilo para los campos del formulario */
    .form-control {
        padding: 12px;
        /* Aumenta el padding dentro de los campos de texto */
        font-size: 16px;
        /* Aumenta el tamaño de la fuente dentro de los campos */
        height: auto;
        /* Asegura que los campos tengan más altura si es necesario */
    }

    /* Estilo para el botón de cerrar */
    #close-form-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        color: #333;
        cursor: pointer;
    }

    #close-create-form-btn,
    #close-update-form-btn {
        position: absolute;
        top: 15px;
        /* Separación desde la parte superior */
        right: 15px;
        /* Separación desde la parte derecha */
        font-size: 24px;
        /* Tamaño de la fuente */
        color: #ff0000;
        /* Color rojo */
        cursor: pointer;
        /* Cambia el cursor al pasar por encima */
        z-index: 1000;
        /* Asegura que esté por encima del contenido */
    }

    /* Estilo para los títulos (encabezados) en azul pastel */
    /* Estilo para los títulos (encabezados) en azul pastel dentro de la tabla */
    table.table th {
        background-color: #3e4567;
        /* Azul pastel */
        color: white;
        /* Texto en blanco */
    }

    /* Estilo para las filas alternadas (gris suave y blanco) dentro de la tabla */
    table.table tbody tr:nth-child(odd) {
        background-color: #f0f0f0;
        /* Gris suave */
    }

    table.table tbody tr:nth-child(even) {
        background-color: white;
        /* Blanco */
    }

    /* Estilo para la tabla (opcional) */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Opcional: agregar un borde suave a las celdas */
    table td,
    table th {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    /* Cambiar el color de fondo al pasar el ratón sobre las filas (opcional) */
    tr:hover {
        background-color: #3e4567;
    }
    </style>
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
                    <h5>Total de Gastos: <span id="total-expenses">$0</span></h5>
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

    <!-- JS Personalizado -->
    <script>
    // Función para cargar los gastos
    function loadExpenses(startDate = '', endDate = '', minAmount = '', maxAmount = '') {
        $.get('index.php?route=7', {
            start_date: startDate,
            end_date: endDate,
            min_amount: minAmount,
            max_amount: maxAmount
        }, function(data) {
            let expenses = JSON.parse(data);
            let expensesList = $('#expenses-list');
            let expensesSelect = $('#select-expense');

            expensesList.empty(); // Limpiar la tabla
            expensesSelect.empty(); // Limpiar el select
            expensesSelect.append('<option value="">Selecciona un gasto...</option>'); // Opción por defecto

            expenses.forEach(expense => {
                expensesSelect.append(
                    `<option value="${expense.id}">${expense.descripcion} - ${expense.fecha}</option>`
                );
                expensesList.append(`
                <tr>
                    <td>${expense.fecha}</td>
                    <td>${expense.descripcion}</td>
                    <td>$${expense.importe}</td>
                    <td><a href="../app/pages/expense/storage/${expense.usuario}/${expense.ticket}" class="btn btn-info btn-sm" target="_blank">Ver Ticket</a></td>
                    <td><button class="btn btn-danger btn-sm" onclick="deleteExpense(${expense.id})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `);
            });

            // Actualizar el total
            let total = expenses.reduce((acc, expense) => acc + parseFloat(expense.importe), 0);
            $('#total-expenses').text('$' + total.toFixed(2));
        });
    }

    // Función para alternar la visibilidad de formularios
    function toggleForm(formId, show = true) {
        document.getElementById(formId).style.display = show ? 'flex' : 'none';
    }

    // Inicialización al cargar la página
    $(document).ready(function() {
        loadExpenses(); // Cargar gastos al inicio

        // Manejar el filtro de fechas e importes
        $('#expense-start-date, #expense-end-date, #expense-min-amount, #expense-max-amount').on('change',
            function() {
                let startDate = $('#expense-start-date').val();
                let endDate = $('#expense-end-date').val();
                let minAmount = $('#expense-min-amount').val();
                let maxAmount = $('#expense-max-amount').val();
                loadExpenses(startDate, endDate, minAmount, maxAmount);
            });

        // Manejar la selección de un gasto para actualizar
        $('#select-expense').on('change', function() {
            let expenseId = $(this).val();
            if (expenseId) {
                $.get('index.php?route=11', {
                    id: expenseId
                }, function(data) {
                    let expense = JSON.parse(data);
                    if (expense.id) {
                        $('#update-expense-id').val(expense.id);
                        $('#update-expense-date').val(expense.fecha);
                        $('#update-expense-description').val(expense.descripcion);
                        $('#update-expense-amount').val(expense.importe);
                        toggleForm('update-form-container', true);
                    } else {
                        alert('No se encontró el gasto.');
                    }
                }).fail(() => alert('Error al obtener los datos del gasto.'));
            }
        });

        // Botón para abrir el formulario de agregar un nuevo registro
        $('#open-create-form-btn').on('click', function() {
            toggleForm('create-form-container', true);
        });

        // Cerrar formularios al hacer clic fuera de ellos
        $('#create-form-container, #update-form-container').on('click', function(e) {
            if (e.target === this) toggleForm(this.id, false);
        });

        // Crear gasto
        $('#create-expense-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: 'index.php?route=9',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    alert('Gasto registrado correctamente.');
                    $('#create-expense-form')[0].reset();
                    toggleForm('create-form-container', false);
                    loadExpenses();
                },
                error: () => alert('Error al registrar el gasto.')
            });
        });

        // Actualizar gasto
        $('#update-expense-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: 'index.php?route=10',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    alert('Gasto actualizado correctamente.');
                    $('#update-expense-form')[0].reset();
                    toggleForm('update-form-container', false);
                    loadExpenses();
                },
                error: () => alert('Error al actualizar el gasto.')
            });
        });

        // Manejar eliminación de un gasto
        window.deleteExpense = function(expenseId) {
            if (confirm('¿Estás seguro de que deseas eliminar este gasto?')) {
                $.ajax({
                    url: 'index.php?route=8',
                    type: 'POST',
                    data: {
                        id: expenseId
                    },
                    success: function() {
                        alert('Gasto eliminado correctamente.');
                        loadExpenses();
                    },
                    error: () => alert('Error al eliminar el gasto.')
                });
            }
        };
    });

    // Botón de cerrar para el formulario de creación
    $('#close-create-form-btn').on('click', function() {
        toggleForm('create-form-container', false);
    });

    // Botón de cerrar para el formulario de actualización
    $('#close-update-form-btn').on('click', function() {
        toggleForm('update-form-container', false);
    });

    // Variables de orden
    let sortOrderDate = true; // true para ascendente, false para descendente
    let sortOrderAmount = true; // true para ascendente, false para descendente

    // Función para alternar las flechas de ordenación
    function toggleArrow(button, isAscending) {
        const icon = button.querySelector('i');
        if (isAscending) {
            icon.classList.remove('fa-arrow-down');
            icon.classList.add('fa-arrow-up');
        } else {
            icon.classList.remove('fa-arrow-up');
            icon.classList.add('fa-arrow-down');
        }
    }

    // Ordenar por fecha
    document.getElementById('sort-date').addEventListener('click', () => {
        sortTableByColumn(0, 'date', sortOrderDate);
        sortOrderDate = !sortOrderDate; // Alternar el orden
        toggleArrow(document.getElementById('sort-date'), sortOrderDate); // Alternar la flecha
    });

    // Ordenar por importe
    document.getElementById('sort-amount').addEventListener('click', () => {
        sortTableByColumn(2, 'amount', sortOrderAmount);
        sortOrderAmount = !sortOrderAmount; // Alternar el orden
        toggleArrow(document.getElementById('sort-amount'), sortOrderAmount); // Alternar la flecha
    });

    // Función para ordenar la tabla por una columna
    function sortTableByColumn(index, type, ascending) {
        const rows = Array.from(document.querySelectorAll('#expenses-list tr'));
        rows.sort((a, b) => {
            const cellA = a.cells[index].textContent.trim();
            const cellB = b.cells[index].textContent.trim();

            // Comparar los valores dependiendo del tipo (fecha o número)
            let compare = 0;
            if (type === 'date') {
                compare = new Date(cellA) - new Date(cellB); // Para fechas
            } else if (type === 'amount') {
                compare = parseFloat(cellA.replace('$', '').replace(',', '')) - parseFloat(cellB.replace('$',
                    '').replace(',', '')); // Para importes
            }

            return ascending ? compare : -compare;
        });

        // Reorganizar las filas en la tabla
        const tbody = document.getElementById('expenses-list');
        rows.forEach(row => tbody.appendChild(row));
    }
    </script>
</body>

</html>