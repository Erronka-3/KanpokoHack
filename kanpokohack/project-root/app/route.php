<?php
// app/route.php

function route($path) {
    // Mapeo de rutas amigables
    $routes = [
        '1' => __DIR__ . '/pages/home/dashboard.php',      // '1' será mapeado a dashboard.php
        '2' => __DIR__ . '/pages/user/profile.php',        // '2' será mapeado a profile.php
        '3' => __DIR__ . '/pages/cards/cards.php',         // '3' será mapeado a cards.php
        '4' => __DIR__ . '/pages/expense/expenses.php',    // '4' será mapeado a expenses.php
        '5' => __DIR__ . '/pages/home/login.php',          // '5' será mapeado a login.php
        '6' => __DIR__ . '/pages/home/logout.php',
        '7' => __DIR__ . '/pages/expense/get_expenses.php',
        '8' => __DIR__ . '/pages/expense/delete_expense.php',
        '9' => __DIR__ . '/pages/expense/register_expense.php',
        '10' => __DIR__ . '/pages/expense/update_expense.php',
        '11' => __DIR__ . '/pages/expense/get_expense_by_id.php',
        '12' => __DIR__ . '/pages/users/roles.php',
       '13' => __DIR__ . '/pages/users/edit_roles.php', 
    ];

    // Si la ruta está definida en el array, incluir el archivo correspondiente
    if (array_key_exists($path, $routes)) {
        require_once $routes[$path];
    } else {
        // Si la ruta no está definida, cargar una página 404
        require_once __DIR__ . '/pages/home/logout.php';
    }
}

?>