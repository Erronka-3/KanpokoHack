<?php
// Redirigir a Keycloak para la autenticación
$client_id = 'your-client-id';
$redirect_uri = 'http://localhost/Kanpokohack/project-root/backend/php/callback.php';
$keycloak_url = 'http://keycloak-server:8080/realms/your-realm/protocol/openid-connect/auth';

// Redirigir al servidor de Keycloak
header('Location: ' . $keycloak_url . '?client_id=' . $client_id . '&response_type=code&redirect_uri=' . urlencode($redirect_uri));
exit;
?>
