<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios</title>
    <link rel="stylesheet" href="../app/assets/css/styles_roles.css">
    <script defer src="../app/assets/js/scripts_roles.js"></script>

<head>
        <?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['access_token'])) {
    header("Location: 'index.php?route=6");
    exit;
}

include '../app/pages/menu/menu.php';

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
    curl_setopt($ch, CURLOPT_SSLCERT, CERT); // Certificado del cliente
    curl_setopt($ch, CURLOPT_SSLKEY, KEY); // Clave privada del cliente
    curl_setopt($ch, CURLOPT_CAINFO, CACERT); // Certificado de la autoridad
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

?>



    <body>
        <?php
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

// Obtener la lista de usuarios desde Keycloak
$usersUrl = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users";
$headers = [
    'Authorization: Bearer ' . $_SESSION['access_token']
];

// Obtener la lista de usuarios desde Keycloak (incluyendo nombres y apellidos)
try {
    $users = makeRequest($usersUrl, $headers);
} catch (Exception $e) {
    echo "Error al obtener la lista de usuarios: " . $e->getMessage();
    exit();
}

// Mostrar la lista de usuarios
echo "<h1>Lista de Usuarios</h1>";
echo "<table border='1' id='userTable'>";
echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Estado</th><th>Acciones</th></tr>";
foreach ($users as $user) {
    $status = $user['enabled'] ? 'Alta' : 'Baja';
    echo "<tr data-user-id='" . htmlspecialchars($user['id']) . "'>";
    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
    echo "<td class='username-column'>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td class='firstName-column'>" . htmlspecialchars($user['firstName'] ?? 'N/A') . "</td>";
    echo "<td class='lastName-column'>" . htmlspecialchars($user['lastName'] ?? 'N/A') . "</td>";
    echo "<td class='email-column'>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
    echo "<td class='status-column'>" . $status . "</td>";
    echo "<td><button onclick=\"openModal('" . htmlspecialchars($user['id']) . "')\">Editar</button></td>";
    echo "</tr>";
}
echo "</table>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $firstName = $_POST['firstName'] ?? null;
    $lastName = $_POST['lastName'] ?? null;
    $email = $_POST['email'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$userId || !$firstName || !$lastName || !$email || !in_array($status, ['alta', 'baja'])) {
        http_response_code(400);
        echo "Datos inválidos.";
        exit();
    }

    $userUrl = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
    $postData = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'enabled' => $status === 'alta'
    ];

    try {
        makeRequest($userUrl, $headers, 'PUT', $postData);
        echo "Usuario actualizado exitosamente.";
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error al actualizar el usuario: " . $e->getMessage();
        exit();
    }
}

?>

        <div id="modal"
            style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5);">
            <div
                style="position: relative; margin: 10% auto; width: 400px; background-color: white; padding: 20px; border-radius: 5px;">
                <h2>Editar Información del Usuario</h2>
                <form id="editForm" onsubmit="updateUser(event)">
                    <input type="hidden" name="user_id" id="user_id">
                    <label for="firstName">Nombre:</label>
                    <input type="text" name="firstName" id="firstName" required>
                    <br><br>
                    <label for="lastName">Apellido:</label>
                    <input type="text" name="lastName" id="lastName" required>
                    <br><br>
                    <label for="email">Correo:</label>
                    <input type="email" name="email" id="email">
                    <br><br>
                    <label for="status">Estado:</label>
                    <select name="status" id="status">
                        <option value="alta">Alta</option>
                        <option value="baja">Baja</option>
                    </select>
                    <br><br>
                    <button type="submit">Actualizar Usuario</button>
                    <button type="button" onclick="closeModal()">Cerrar</button>
                </form>
            </div>
        </div>

    </body>

</html>