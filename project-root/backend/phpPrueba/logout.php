<?php
require_once 'config.php';

// Destruir la sesión del usuario en la aplicación
session_start();
session_unset();
session_destroy();


// Generar la URL de logout correctamente
$logout_url = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout?" . http_build_query([
    'client_id' => CLIENT_ID,
    '$post_logout_redirect_uri' => REDIRECT_LOGOUT_URI // Configurada correctamente en Keycloak
]);

// Redirigir al usuario a Keycloak para logout
header("Location: $logout_url");
exit;