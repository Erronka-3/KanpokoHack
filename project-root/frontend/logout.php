<?php
include('../backend/phpPrueba/config.php'); // Cargar configuración necesaria

// Función para obtener las URLs de Keycloak
function getKeycloakUrls($hostname) {
    if (strpos($hostname, 'localhost') !== false) {
        return [
            'logout' => KEYCLOAK_URL . '/realms/' . REALM . '/protocol/openid-connect/logout',
            'redirectAfterLogout' => REDIRECT_LOGOUT_URI
        ];
    } else {
        // Ajustar para otros entornos si es necesario
        return [
            'logout' => KEYCLOAK_URL . '/realms/' . REALM . '/protocol/openid-connect/logout',
            'redirectAfterLogout' => REDIRECT_LOGOUT_URI
        ];
    }
}

// Obtener URLs dinámicamente
$urls = getKeycloakUrls($_SERVER['HTTP_HOST']);

// Crear las opciones de logout
$logoutOptions = [
    'redirect_uri' => $urls['redirectAfterLogout']
];

// Generar URL de logout con parámetros
$logoutUrl = $urls['logout'] . '?' . http_build_query($logoutOptions);

// Registrar URL para depuración (opcional)
file_put_contents('logout_debug.log', $logoutUrl);

// Redirigir al usuario a Keycloak para cerrar sesión
header('Location: ' . $logoutUrl);
exit();
?>












