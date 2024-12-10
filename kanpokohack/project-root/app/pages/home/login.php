<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
// index.php
include('../config/config.php');

// Generamos el URL de Keycloak para iniciar el proceso de autenticación
$authUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/auth?" . http_build_query([
    'response_type' => 'code', // Tipo de respuesta esperada
    'client_id' => CLIENT_ID, // ID del cliente
    'redirect_uri' => REDIRECT_URI, // URL de redirección después de la autenticación
    'scope' => 'openid' // Solicitamos acceso al perfil del usuario
]);

// Redirigir al usuario a Keycloak para login
header('Location: ' . $authUrl);
exit;