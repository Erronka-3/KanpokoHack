<?php
// index.php
include('config.php');

// Verificamos si el parámetro logout está presente en la URL
$is_logged_out = isset($_GET['logout']) && $_GET['logout'] == 'true';

if ($is_logged_out) {
    // Redirigir a Keycloak para el login después del logout
    $authUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/auth?" . http_build_query([
        'response_type' => 'code', // Tipo de respuesta esperada
        'client_id' => CLIENT_ID, // ID del cliente
        'redirect_uri' => REDIRECT_URI, // URL de redirección después de la autenticación
        'scope' => 'openid' // Solicitamos acceso al perfil del usuario
    ]);

    // Redirigir a la URL de Keycloak
    header('Location: ' . $authUrl);
    exit; // Asegurarse de que el script no continúe
}

// Si no estamos en la página de logout, redirigir al usuario a Keycloak para login
$authUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/auth?" . http_build_query([
    'response_type' => 'code',
    'client_id' => CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'scope' => 'openid'
]);

// Redirigir al usuario a Keycloak para login
header('Location: ' . $authUrl);
exit;
?>
