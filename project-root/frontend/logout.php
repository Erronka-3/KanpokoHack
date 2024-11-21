<?php
// Incluir config.php para tener acceso a las constantes definidas
include('../backend/phpPrueba/config.php'); // Asegúrate de que la ruta es correcta

session_start();

// Eliminar el token de sesión y cualquier dato relacionado
session_unset();
session_destroy();

// Definir la URL de redirección después del logout
$redirectUri = urlencode("http://localhost/KanpokoHack/project-root/backend/phpPrueba");

// URL de Keycloak para cerrar sesión
$logoutUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/logout?" . http_build_query([
    'redirect_uri' => $redirectUri
]);

// Redirigir al usuario al URL de logout de Keycloak
header('Location: ' . $logoutUrl);
exit();
?>

?>





