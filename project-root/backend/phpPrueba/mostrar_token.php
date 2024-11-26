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

    // Extraer el email desde el token y guardarlo en la sesión
    if (isset($decodedToken['payload']['email'])) {
        $_SESSION['user_email'] = $decodedToken['payload']['email'];
    } else {
        $_SESSION['user_email'] = ''; // Si no hay email, guardar vacío
    }

    // Extraer el nombre desde el token y guardarlo en la sesión
    if (isset($decodedToken['payload']['preferred_username'])) {
        $_SESSION['user_name'] = $decodedToken['payload']['preferred_username'];
    } else {
        $_SESSION['user_name'] = ''; // Si no hay nombre, guardar vacío
    }

    // Redirigir al dashboard
    header("Location: /KanpokoHack/project-root/frontend/dashboard.php");
    exit();
} catch (Exception $e) {
    echo "Error al decodificar el token: " . $e->getMessage();
    exit();
}