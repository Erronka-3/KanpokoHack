<?php
session_start();

// Verificar si hay un token disponible
if (!isset($_SESSION['access_token'])) {
    echo "No hay token en la sesión. Por favor, inicia sesión.";
    exit();
}

$accessToken = $_SESSION['access_token'];

/**
 * Función para decodificar un JWT sin validar la firma.
 */
function decodeJwt($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception("El token no tiene un formato válido.");
    }

    $header = json_decode(base64_decode($parts[0]), true);
    $payload = json_decode(base64_decode($parts[1]), true);

    return [
        'header' => $header,
        'payload' => $payload,
    ];
}

try {
    $decodedToken = decodeJwt($accessToken);

    // Extraer roles desde el token
    $roles = $decodedToken['payload']['resource_access']['public_client']['roles'] ?? [];
    $_SESSION['user_roles'] = $roles;

    // Redirigir al dashboard
    header("Location: /KanpokoHack/project-root/frontend/dashboard.php");
    exit();
} catch (Exception $e) {
    echo "Error al decodificar el token: " . $e->getMessage();
    exit();
}

