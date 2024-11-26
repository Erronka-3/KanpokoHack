<?php
session_start();
include 'menu.php';
include('config.php');

// Verificar si hay un token disponible en la sesión
if (!isset($_SESSION['access_token'])) {
    echo "No hay token en la sesión. Por favor, inicia sesión.";
    exit();
}

// Función para obtener un token de administrador
function getAdminToken() {
    $tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
    $postData = [
        'grant_type' => 'client_credentials',
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode !== 200) {
        throw new Exception("Error al obtener el token de administrador: Código $statusCode, Respuesta: $response");
    }

    $tokenData = json_decode($response, true);
    if (isset($tokenData['access_token'])) {
        return $tokenData['access_token'];
    } else {
        throw new Exception("No se pudo obtener el token. Respuesta: $response");
    }
}

// Función para obtener los datos actualizados del usuario desde Keycloak
function getUserData($userId, $adminToken) {
    $url = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
    
    $headers = [
        "Authorization: Bearer $adminToken",
        "Content-Type: application/json",
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode !== 200) {
        throw new Exception("Error al obtener los datos del usuario. Código: $statusCode, Respuesta: $response");
    }

    // Decodificar la respuesta JSON
    $userData = json_decode($response, true);
    
    return $userData;
}

// Decodificar el token de usuario
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
    // Decodificar el token de usuario actual
    $decodedToken = decodeJwt($_SESSION['access_token']);

    // Obtener el ID del usuario desde el token
    $userId = $decodedToken['payload']['sub']; // ID del usuario

    // Obtener el token de administrador
    $adminToken = getAdminToken();

    // Obtener los datos actualizados del usuario desde Keycloak
    $userData = getUserData($userId, $adminToken);
    
    // Obtener los datos del usuario actualizados
    $currentEmail = $userData['email'] ?? '';
    $currentUsername = $userData['username'] ?? '';

    // Procesar la solicitud del formulario para modificar los datos
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newEmail = $_POST['email'];
        $newUsername = $_POST['username'];

        // Actualizar los datos del usuario en Keycloak
        $url = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
        $data = [
            'email' => $newEmail,
            'username' => $newUsername,
        ];

        $headers = [
            "Authorization: Bearer $adminToken",
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode === 204) {
            echo "Datos actualizados correctamente.";

            // Después de actualizar, obtener los datos actualizados
            $userData = getUserData($userId, $adminToken);
            $currentEmail = $userData['email'] ?? '';
            $currentUsername = $userData['username'] ?? '';
        } else {
            echo "Error al actualizar los datos. Código de respuesta: $statusCode<br>Detalles del error: " . $response;
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!-- Formulario para modificar los datos del usuario -->
<form method="POST">
    <label for="email">Correo electrónico actual:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($currentEmail); ?>" required>
    <br>

    <label for="username">Nombre de usuario actual:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
    <br>

    <button type="submit">Actualizar datos</button>
</form>
