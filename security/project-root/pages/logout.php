<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include('../config/config.php');

// Destruir la sesión del usuario en la aplicación
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy();



// Generar la URL de logout utilizando los datos del config.php
$logout_url = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout" .
    "?client_id=" . CLIENT_ID .
    "&post_logout_redirect_uri=" . urlencode(REDIRECT_URI_LOGOUT);

// Redirigir al usuario a la URL de logout de Keycloak
header("Location: $logout_url");
exit();
?>