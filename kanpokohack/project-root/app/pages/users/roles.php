<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles de usuarios</title>
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    
    <style>
        /* Estilos generales para la tabla */
        table.table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        /* Estilo para los encabezados de la tabla */
        table.table th {
            background-color: #3e4567; /* Azul pastel */
            color: white; /* Texto en blanco */
            padding: 12px 15px; /* Relleno para que se vea más espacioso */
            text-align: left;
        }

        /* Estilo para las filas alternadas (gris suave y blanco) dentro de la tabla */
        table.table tbody tr:nth-child(odd) {
            background-color: #f0f0f0; /* Gris suave */
        }

        table.table tbody tr:nth-child(even) {
            background-color: white; /* Blanco */
        }

        /* Estilo para las celdas de la tabla */
        table td, table th {
            border: 1px solid #ddd; /* Borde suave */
            padding: 10px 15px; /* Relleno para mayor espacio */
            text-align: left;
        }

        /* Cambiar el color de fondo al pasar el ratón sobre las filas */
        td:hover {
         background-color: #dff9d1; /* Gris claro */
        }

        /* Estilo para los botones dentro de la tabla */
        button {
            padding: 6px 12px;
            background-color: #4CAF50; /* Verde */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049; /* Verde oscuro al pasar el ratón */
        }

        /* Opcional: margen alrededor de la tabla para separarla de otros elementos */
        table.table {
            margin-top: 30px;
        }
    </style>
</head>
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

try {
    $users = makeRequest($usersUrl, $headers);
} catch (Exception $e) {
    echo "Error al obtener la lista de usuarios: " . $e->getMessage();
    exit();
}

// Mostrar la lista de usuarios
echo "<h1>Lista de Usuarios</h1>";
echo "<table border='1' id='userTable'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Acciones</th></tr>";
foreach ($users as $user) {
    $status = $user['enabled'] ? 'Alta' : 'Baja';
    echo "<tr data-user-id='" . htmlspecialchars($user['id']) . "'>";
    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
    echo "<td class='status-column'>" . $status . "</td>";
    echo "<td><button onclick=\"openModal('" . htmlspecialchars($user['id']) . "')\">Editar Estado</button></td>";
    echo "</tr>";
}
echo "</table>";

// Si se recibió una solicitud POST para actualizar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$userId || !in_array($status, ['alta', 'baja'])) {
        http_response_code(400);
        echo "Datos inválidos.";
        exit();
    }

    $userUrl = KEYCLOAK_URL . "/admin/realms/" . REALM . "/users/$userId";
    $newState = ($status === 'alta') ? true : false;
    $postData = ['enabled' => $newState];

    try {
        makeRequest($userUrl, $headers, 'PUT', $postData);
        echo "Estado actualizado exitosamente.";
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error al actualizar el estado del usuario: " . $e->getMessage();
        exit();
    }
}
?>

<div id="modal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5);">
    <div style="position: relative; margin: 10% auto; width: 400px; background-color: white; padding: 20px; border-radius: 5px;">
        <h2>Editar Estado de Usuario</h2>
        <form id="editForm" onsubmit="updateUserStatus(event)">
            <input type="hidden" name="user_id" id="user_id">
            <label for="status">Estado:</label>
            <select name="status" id="status">
                <option value="alta">Alta</option>
                <option value="baja">Baja</option>
            </select>
            <button type="submit">Actualizar Estado</button>
            <button type="button" onclick="closeModal()">Cerrar</button>
        </form>
    </div>
</div>

<script>
function openModal(userId) {
    document.getElementById('user_id').value = userId;
    document.getElementById('modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

async function updateUserStatus(event) {
    event.preventDefault();

    const userId = document.getElementById('user_id').value;
    const status = document.getElementById('status').value;

    try {
        const response = await fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                user_id: userId,
                status: status
            })
        });

        if (response.ok) {
            closeModal();

            // Actualizar el estado en la tabla
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (userRow) {
                userRow.querySelector('.status-column').textContent = status === 'alta' ? 'Alta' : 'Baja';
            }
        } else {
            console.error('Error al actualizar el estado:', response.statusText);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
}
</script>
