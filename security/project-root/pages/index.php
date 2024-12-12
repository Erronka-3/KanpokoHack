<?php
// public/index.php

// Requerir el archivo de rutas
require_once 'route.php';

// Verificamos si existe un parámetro `route` en la URL
$path = isset($_GET['route']) ? $_GET['route'] : '5'; // Redirige al login si no se especifica una ruta

// Ejecutamos la función route() para manejar la solicitud
route($path);

?>