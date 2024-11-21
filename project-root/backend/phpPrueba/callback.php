<?php
include('config.php');
session_start();

// Verificar si se recibió el código de autorización
if (!isset($_GET['code'])) {
    echo "Error: No se recibió el código de autorización.";
    exit();
}

$authCode = $_GET['code'];

// Preparar la solicitud para obtener el token
$tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
$postData = [
    'grant_type' => 'authorization_code',
    'code' => $authCode,
    'redirect_uri' => REDIRECT_URI,
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET // Solo si el cliente es confidencial
];

// Enviar la solicitud POST
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseCode === 200) {
    // Decodificar el token y guardarlo en sesión
    $tokenData = json_decode($response, true);
    $_SESSION['access_token'] = $tokenData['access_token'];

    // Redirigir al usuario a la página de inicio
    header("Location: mostrar_token.php");
    exit();
} else {
    echo "Error al obtener el token: " . $response;
}
