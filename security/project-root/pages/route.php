<?php
// app/route.php

function route($path) {
    // Mapeo de rutas amigables
    $routes = [
        '1' =>  'security.php',      
        '5' => 'login.php',          
        '6' => 'logout.php',
        
    ];

    // Si la ruta está definida en el array, incluir el archivo correspondiente
    if (array_key_exists($path, $routes)) {
        require_once $routes[$path];
    } else {
        // Si la ruta no está definida, cargar una página 404
        require_once  'logout.php';
    }
}

?>