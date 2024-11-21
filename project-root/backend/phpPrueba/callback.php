<?php
include('config.php');

// Verificar si Keycloak ha enviado el código de autorización
if (isset($_GET['code'])) {
    $authCode = $_GET['code'];

    $tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
    $postData = [
        'grant_type' => 'authorization_code',
        'code' => $authCode,
        'redirect_uri' => REDIRECT_URI,
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

    $response = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseCode == 200) {
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'];

        // Decodificar el token JWT (información contenida en el payload)
        $parts = explode('.', $accessToken);
        if (count($parts) === 3) {
            list($header, $payload, $signature) = $parts;

            // Decodificar Base64
            $decodedPayload = json_decode(base64_decode($payload), true);

            echo "<h1>Información del Usuario</h1>";
            echo "<pre>" . json_encode($decodedPayload, JSON_PRETTY_PRINT) . "</pre>";

            // Verificar si el usuario tiene el rol "Admin"
            if (isset($decodedPayload['realm_access']['roles']) && in_array('Admin', $decodedPayload['realm_access']['roles'])) {
                echo "<h2>Información Exclusiva para Admin:</h2>";
                echo "<p>Bienvenido, Admin. Aquí está la información confidencial.</p>";
            } else {
                echo "<h2>Acceso Restringido:</h2>";
                echo "<p>No tienes permisos para ver esta información.</p>";
            }
        } else {
            echo "<p>Error: el token no parece ser un JWT válido.</p>";
        }
    } else {
        echo "<p>Error al obtener el token: " . htmlspecialchars($response) . "</p>";
    }
} else {
    echo "<p>Error: no se recibió código de autorización.</p>";
}