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

// Verificamos si la URL contiene el parámetro de logout
$is_logged_out = isset($_GET['logout']) && $_GET['logout'] == 'true';

if ($is_logged_out) {
    echo "<h1>You have been logged out</h1>";
    echo "<p>Thank you for using our service.</p>";

    // Mostrar el botón de login
    echo '<a href="' . $authUrl . '"><button>Login Again</button></a>';
    exit; // Salir del script para evitar la redirección automática
}

// Si no estamos en la página de logout, redirigir al usuario a Keycloak para login
header('Location: ' . $authUrl);
exit;
?>
