<?php
// callback.php
include('config.php');

// Verificar si Keycloak ha enviado el código de autorización
if (isset($_GET['code'])) {
    // Obtener el código de autorización
    $authCode = $_GET['code'];

    // Preparar los datos para intercambiar el código por un token
    $tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
    $postData = [
        'grant_type' => 'authorization_code',
        'code' => $authCode,
        'redirect_uri' => REDIRECT_URI,
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET // Solo si es un cliente confidencial
    ];

    // Inicializar cURL para hacer la solicitud POST
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

    // Ejecutar la solicitud
    $response = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseCode == 200) {
        // La respuesta es un JSON que contiene el token
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'];

        // Mostrar el token o usarlo para obtener información adicional
        echo "Token de acceso: " . $accessToken;
        // Aquí podrías hacer algo más, como obtener información del usuario, etc.
    } else {
        // Si algo salió mal, mostrar el error
        echo "Error al obtener el token: " . $response;
    }
} else {
    // Si no hay código de autorización en la URL
    echo "Error: no se recibió código de autorización";
}
