<?php
// Configuración de Keycloak
$client_id = 'your-client-id';
$client_secret = 'your-client-secret';
$redirect_uri = 'http://localhost/Kanpokohack/project-root/backend/php/callback.php';
$keycloak_token_url = 'http://keycloak-server:8080/realms/your-realm/protocol/openid-connect/token';

// Obtener el código de autorización
$code = $_GET['code'];

// Intercambiar el código por el token de acceso
$data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($keycloak_token_url, false, $context);

// Convertir la respuesta en formato JSON
$token_data = json_decode($response, true);

// El token de acceso está en 'access_token'
$access_token = $token_data['access_token'];

// Puedes ahora usar el token para autenticar al usuario
echo "Token de acceso: " . $access_token;
?>
