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
                <td>${expense.importe} €</td>
                <td><a href="../app/pages/expense/storage/${expense.usuario}/${expense.ticket}" class="btn btn-info btn-sm" target="_blank">Ver Ticket</a></td>
                <td><button class="btn btn-danger btn-sm" onclick="deleteExpense(${expense.id})"><i class="fas fa-trash"></i></button></td>
            </tr>
        `);
        });

        // Actualizar el total
        let total = expenses.reduce((acc, expense) => acc + parseFloat(expense.importe), 0);
        $('#total-expenses').text(total.toFixed(2) + ' €');
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