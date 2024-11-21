<?php
include('../backend/phpPrueba/config.php');

session_start();

// Eliminar sesión
session_unset();
session_destroy();

// URL de logout
$logoutUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout?redirect_uri=" . rawurlencode(REDIRECT_LOGOUT_URI);

// Depurar URL generada
file_put_contents('logout_debug.log', $logoutUrl);

// Inicializar cURL para capturar respuesta de Keycloak
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $logoutUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Registrar respuesta en un archivo
file_put_contents('logout_response.log', "HTTP Code: $httpCode\nResponse: $response");

// Redirigir al logout de Keycloak
header('Location: ' . $logoutUrl);
exit();
?>












