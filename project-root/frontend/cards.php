<?php
include('../backend/phpPrueba/config.php');
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'user1') {
    echo "Error: No estás autenticado. Por favor, inicia sesión.";
    exit();
}

// Definir la URL de la API para obtener información de las tarjetas
$apiUrl = "http://10.11.0.25:4000/infocards1/user1"; // URL para obtener información de las tarjetas

// Realizar la solicitud GET al recurso infocards1
$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    echo "Error: No se pudo obtener la información de la API.";
    exit();
}

// Decodificar la respuesta (base64)
$decodedData = base64_decode($response);
if ($decodedData === false) {
    echo "Error: Fallo en la decodificación Base64.";
    exit();
}

// Clave AES-256 proporcionada de manera segura (esto debe ser recibido de manera confidencial)
$keyString = 'KeyMustBe16ByteOR24ByteOR32ByT1!'; // Sustituir con la clave real

// Función para descifrar los datos (AES-256 en modo ECB)
function decryptData($data, $key) {
    return openssl_decrypt($data, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
}

// Intentar descifrar los datos cifrados
$decryptedData = decryptData($decodedData, $keyString);

if ($decryptedData === false) {
    echo "Error al descifrar los datos: " . openssl_error_string();
    exit();
}

// Convertir los datos descifrados de JSON a un array
$cardInfo = json_decode($decryptedData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al convertir los datos JSON: " . json_last_error_msg();
    exit();
}

// Procesar la solicitud para obtener información o cambiar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['get_info'])) {
        // Obtener la información de la tarjeta usando el número de tarjeta
        $card_number = $_POST['card_number']; // Ahora se usa el número de la tarjeta
        // Comprobamos si el número de tarjeta existe
        if ($card_number === $cardInfo['card_debit'] || $card_number === $cardInfo['card_credit']) {
            $card_info = $cardInfo; // Usamos la información descifrada
        } else {
            $card_info_error = "No se encontró una tarjeta con ese número.";
        }
    } elseif (isset($_POST['enable_cards'])) {
        // Verificar cuál tarjeta se seleccionó para habilitar
        $card_type = $_POST['card_type'];  // Tipo de tarjeta (DEBIT o CREDIT)

        // Solo proceder si la tarjeta seleccionada no está activa
        if (($card_type === 'DEBIT' && $cardInfo['current_card'] !== 'DEBIT') ||
            ($card_type === 'CREDIT' && $cardInfo['current_card'] !== 'CREDIT')) {

            // URL de la API para habilitar/deshabilitar las tarjetas
            $apiEnableCardsUrl = "http://10.11.0.25:4000/enablecard1/user1/{$card_type}"; // Usar la ruta con tipo de tarjeta

            // Inicializar cURL para hacer la solicitud GET
            $ch = curl_init($apiEnableCardsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Ejecutar la solicitud
            $apiResponse = curl_exec($ch);

            if(curl_errno($ch)) {
                echo 'Error en la solicitud cURL: ' . curl_error($ch);
            } else {
                $responseData = json_decode($apiResponse, true);
                if ($responseData && isset($responseData['message'])) {
                    $state_message = $responseData['message']; // Mensaje de la API
                    // Actualizamos el estado de la tarjeta después de cambiarlo
                    $cardInfo['current_card'] = $card_type; // Asumimos que el cambio fue exitoso
                } else {
                    $state_message = "Hubo un error al intentar cambiar el estado de las tarjetas.";
                }
            }
            curl_close($ch);

            // Redirigir para evitar la repetición de la solicitud al recargar la página
            header("Location: cards.php");
            exit();
        } else {
            $state_message = "La tarjeta seleccionada ya está activa.";
        }
    }
}

// Determinar qué tarjeta está desactivada
$inactive_card = null;
if ($cardInfo['current_card'] === 'DEBIT') {
    $inactive_card = 'CREDIT'; // Si la tarjeta de débito está activada, la de crédito está desactivada
} elseif ($cardInfo['current_card'] === 'CREDIT') {
    $inactive_card = 'DEBIT'; // Si la tarjeta de crédito está activada, la de débito está desactivada
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tarjetas</title>
</head>
<body>
    <h1>Gestión de Información de Tarjetas</h1>

    <!-- Formulario para obtener información de la tarjeta -->
    <h2>Consultar Información de la Tarjeta</h2>
    <form action="cards.php" method="POST">
        <label for="card_number">Número de la tarjeta:</label>
        <input type="text" id="card_number" name="card_number" required>
        <button type="submit" name="get_info">Obtener Información</button>
    </form>

    <?php if (isset($card_info)): ?>
        <h2>Información de la tarjeta (Número: <?php echo htmlspecialchars($card_number); ?>)</h2>
        <ul>
            <li><strong>Última actividad tarjeta débito:</strong> <?php echo htmlspecialchars($card_info['lastactive_debit']); ?></li>
            <li><strong>Última actividad tarjeta crédito:</strong> <?php echo htmlspecialchars($card_info['lastactive_credit']); ?></li>
            <li><strong>Número tarjeta débito:</strong> <?php echo htmlspecialchars($card_info['card_debit']); ?></li>
            <li><strong>Número tarjeta crédito:</strong> <?php echo htmlspecialchars($card_info['card_credit']); ?></li>
            <li><strong>Tarjeta actual activada:</strong> <?php echo htmlspecialchars($card_info['current_card']); ?></li>
        </ul>
    <?php elseif (isset($card_info_error)): ?>
        <p><?php echo $card_info_error; ?></p>
    <?php endif; ?>

    <!-- Formulario para habilitar o deshabilitar tarjetas -->
    <h2>Habilitar o Deshabilitar Tarjetas</h2>
    <form action="cards.php" method="POST">
        <label for="card_type">Tipo de tarjeta:</label>
        <!-- Solo mostramos la tarjeta desactivada -->
        <select id="card_type" name="card_type" required>
            <option value="<?php echo htmlspecialchars($inactive_card); ?>"><?php echo $inactive_card; ?></option>
        </select>
        <button type="submit" name="enable_cards">Habilitar Tarjeta</button>
    </form>

    <?php if (isset($state_message)): ?>
        <p><?php echo $state_message; ?></p>
    <?php endif; ?>

    <!-- Información de las tarjetas (nombre, estado y tipo) -->
    <h2>Información de las Tarjetas</h2>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Tipo de Tarjeta</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($cardInfo['card_debit']); ?></td>
            <td><?php echo $cardInfo['current_card'] === 'DEBIT' ? 'ACTIVA' : 'INACTIVA'; ?></td>
            <td>Débito</td>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($cardInfo['card_credit']); ?></td>
            <td><?php echo $cardInfo['current_card'] === 'CREDIT' ? 'ACTIVA' : 'INACTIVA'; ?></td>
            <td>Crédito</td>
        </tr>
    </table>
</body>
</html>
