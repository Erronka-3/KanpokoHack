<?php
// index.php
include('config.php');

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