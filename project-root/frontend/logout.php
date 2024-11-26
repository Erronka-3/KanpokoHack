<?php
require_once '../backend/phpPrueba/config.php';

// Destruir la sesión del usuario en la aplicación
session_start();
session_destroy();

// Generar la URL de logout utilizando los datos del config.php
$logout_url = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout" .
    "?client_id=" . CLIENT_ID .
    "&post_logout_redirect_uri=" . urlencode(REDIRECT_LOGOUT_URI);

// Redirigir al usuario a la URL de logout de Keycloak
header("Location: $logout_url");
exit;











