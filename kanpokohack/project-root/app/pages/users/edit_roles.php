<?php
session_start();

// Incluir configuración
include(__DIR__ . '/../../../config/config.php');

// Función para realizar solicitudes cURL
function makeRequest($url, $headers = [], $method = 'GET', $postData = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($postData) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Type: application/json']));
    }
    curl_setopt($ch, CURLOPT_SSLCERT,CERT); // Certificado del cliente
    curl_setopt($ch, CURLOPT_SSLKEY, KEY); // Clave privada del cliente
    curl_setopt($ch, CURLOPT_CAINFO, CACERT); // Certificado de la autoridad

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifica el certificado del servidor
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode !== 200) {
        throw new Exception("Error en la solicitud cURL. Código: $statusCode, Respuesta: $response");
    }

    return json_decode($response, true);
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['access_token'])) {
    echo "Error: Usuario no autenticado.";
    exit();
}

// Decodificar el token para verificar el rol
function decodeJwt($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception("El token no tiene un formato válido.");
    }
    return json_decode(base64_decode($parts[1]), true);
}

try {
    $decodedToken = decodeJwt($_SESSION['access_token']);
} catch (Exception $e) {
    echo "Error al decodificar el token: " . $e->getMessage();
    exit();
}

// Verificar si el usuario tiene el rol 'admin'
if (!in_array('admin', $decodedToken['realm_access']['roles'] ?? [])) {
    echo "Error: Acceso denegado. Necesitas el rol 'admin'.";
    exit();
}

// Obtener el ID del usuario
$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    echo "Error: ID de usuario no proporcionado.";
    exit();
}

// Lógica de actualización de estado del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $status = $_POST['status'];  // Alta o Baja

    // Determinar la URL de cambio de estado
    $userStatusUrl = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
    $headers = [
        'Authorization: Bearer ' . $_SESSION['access_token']
    ];

    // Cambiar el estado del usuario
    $postData = [
        'enabled' => ($status === 'alta') // Si es "alta", habilitar el usuario
    ];

    try {
        $response = makeRequest($userStatusUrl, $headers, 'PUT', $postData);
        echo "El estado del usuario ha sido actualizado correctamente.";
    } catch (Exception $e) {
        echo "Error al actualizar el estado del usuario: " . $e->getMessage();
    }
} else {
    // Mostrar el formulario de edición de estado
    echo "<h1>Editar Estado para el Usuario $userId</h1>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($userId) . "'>";

    // Mostrar el estado actual del usuario
    $userDetailsUrl = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
    $headers = [
        'Authorization: Bearer ' . $_SESSION['access_token']
    ];

    try {
        $userDetails = makeRequest($userDetailsUrl, $headers);
        $currentStatus = $userDetails['enabled'] ? 'alta' : 'baja';
    } catch (Exception $e) {
        echo "Error al obtener el estado del usuario: " . $e->getMessage();
        exit();
    }

    // Formulario para cambiar el estado del usuario
    echo "<label for='status'>Estado:</label>";
    echo "<select name='status'>";
    echo "<option value='alta'" . ($currentStatus === 'alta' ? ' selected' : '') . ">Alta</option>";
    echo "<option value='baja'" . ($currentStatus === 'baja' ? ' selected' : '') . ">Baja</option>";
    echo "</select>";

    echo "<button type='submit'>Actualizar Estado</button>";
    echo "</form>";
}
?>
