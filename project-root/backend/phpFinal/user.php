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
    return json_decode($response, true);
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
    $currentUsername = isset($userData['username']) ? $userData['username'] : 'No disponible';
    $registrationDate = isset($userData['createdTimestamp']) ? date('Y-m-d H:i:s', $userData['createdTimestamp'] / 1000) : 'No disponible';

    // Inicializar variable para mensaje de éxito
    $updateMessage = '';

    // Procesar la solicitud del formulario para modificar los datos
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newEmail = $_POST['email'];
        $newFirstName = $_POST['firstName'];
        $newLastName = $_POST['lastName'];

        // Actualizar los datos del usuario en Keycloak
        $url = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
        $data = [
            'email' => $newEmail,
            'firstName' => $newFirstName,
            'lastName' => $newLastName
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
            $updateMessage = "Datos actualizados correctamente.";

            // Después de actualizar, obtener los datos actualizados
            $userData = getUserData($userId, $adminToken);
            $currentEmail = $userData['email'] ?? '';
            $currentUsername = isset($userData['username']) ? $userData['username'] : 'No disponible';
        } else {
            $updateMessage = "Error al actualizar los datos. Código de respuesta: $statusCode<br>Detalles del error: " . $response;
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!-- Agregar CSS para mejorar la apariencia -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #4CAF50;
        margin-top: 20px;
    }

    .form-container {
        width: 70%;
        margin: 0 auto;
        background-color: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .form-row .form-group {
        flex: 1 1 45%;
    }

    .form-container label {
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }

    .form-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .form-container input[disabled] {
        background-color: #f2f2f2;
    }

    .form-container button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    .form-container button:hover {
        background-color: #45a049;
    }

    .form-container .message {
        text-align: center;
        margin-top: 20px;
        color: green;
    }

    /* Estilos para el mensaje de éxito */
    .message-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Fondo transparente */
        display: none;
        justify-content: center;
        align-items: center;
    }

    .message-box {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        font-size: 16px;
    }
</style>

<!-- Formulario para mostrar y editar los datos del usuario -->
<div class="form-container">
    <h2>Editar Datos de Usuario</h2>
    <?php if ($updateMessage): ?>
        <div class="message"><?php echo $updateMessage; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?php echo $currentEmail; ?>" required />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username" value="<?php echo $currentUsername; ?>" required disabled />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="firstName">Nombre</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo $userData['firstName'] ?? ''; ?>" required />
            </div>

            <div class="form-group">
                <label for="lastName">Apellido</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo $userData['lastName'] ?? ''; ?>" required />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fecha de Registro</label>
                <input type="text" value="<?php echo $registrationDate; ?>" disabled />
            </div>
        </div>

        <button type="submit">Actualizar</button>
    </form>
</div>

<!-- Contenedor del mensaje -->
<div class="message-container" id="messageContainer" style="display:none;">
    <div class="message-box">
        <p>Datos actualizados correctamente</p>
    </div>
</div>

<script>
    // Mostrar el mensaje de éxito si la actualización fue exitosa
    <?php if ($updateMessage): ?>
        document.getElementById("messageContainer").style.display = "flex";
        // Redirigir después de 2 segundos si la actualización fue exitosa
        setTimeout(function() {
            window.location.href = "user.php"; // Cambia a la URL de destino
        }, 2000);
    <?php endif; ?>
</script>
