<?php
// logout.php
include('../backend/phpPrueba/config.php'); // Asegúrate de que la ruta es correcta

session_start();

// Eliminar el token de sesión y cualquier dato relacionado
session_unset();   // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Codifica manualmente la URL de redirección
$encodedRedirectUri = rawurlencode(REDIRECT_LOGOUT_URI);

// Construye la URL de logout
$logoutUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout?redirect_uri=" . $encodedRedirectUri;

// Registrar la URL generada para depuración
file_put_contents('logout_debug.log', $logoutUrl);

// Redirigir al usuario al URL de logout de Keycloak
header('Location: ' . $logoutUrl);
exit();
?>











