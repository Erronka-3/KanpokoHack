<?php
// Iniciar la sesión
session_start();
include 'menu.php';
// Verificar si el usuario está autenticado
if (!isset($_SESSION['access_token'])) {
    die("<div class='alert alert-danger text-center'>No estás autenticado.</div>");
}

// Obtener el nombre de usuario desde la sesión o desde la URL (GET)
$username = $_SESSION['preferred_username'] ?? 'Usuario desconocido'; // Si no está definido, mostrar un valor por defecto


// Clave de desencriptación
define('SECRET_KEY', 'KeyMustBe16ByteOR24ByteOR32ByT1!');

// Función para desencriptar los datos usando AES-256 en modo ECB
function decryptData($encryptedData) {
    $decodedData = base64_decode($encryptedData);
    if ($decodedData === false) {
        throw new Exception("Fallo al decodificar los datos en Base64.");
    }

    $decryptedData = openssl_decrypt($decodedData, 'AES-256-ECB', SECRET_KEY, OPENSSL_RAW_DATA);
    if ($decryptedData === false) {
        throw new Exception("Fallo al desencriptar los datos.");
    }

    return json_decode($decryptedData, true);
}

// Hacer la solicitud a la API para obtener la información encriptada
$apiUrl = 'http://localhost:4000/infocards1/' . $username;
$response = file_get_contents($apiUrl);

if ($response === false) {
    die("<div class='alert alert-danger text-center'>Error al obtener los datos de la API.</div>");
}

// Desencriptar la respuesta
try {
    $decryptedData = decryptData($response);
} catch (Exception $e) {
    die("<div class='alert alert-danger text-center'>Ocurrió un error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Preparar los datos para la tabla
$cards = [];
if (isset($decryptedData['records'][0]['error']) && $decryptedData['records'][0]['error'] === 'User not found') {
    $message = "No hay información disponible para este usuario.";
} else {
    $message = "";

    // Determinar el estado de las tarjetas basado en current_card
    $currentCard = $decryptedData['current_card'] ?? '';
    $debitStatus = ($currentCard === 'DEBIT') ? 'Activo' : 'Inactivo';
    $creditStatus = ($currentCard === 'CREDIT') ? 'Activo' : 'Inactivo';

    $cards = [
        [
            'number' => $decryptedData['card_debit'] ?? 'N/A',
            'status' => $debitStatus,
            'lastTransaction' => $decryptedData['lastactive_debit'] ?? 'N/A'
        ],
        [
            'number' => $decryptedData['card_credit'] ?? 'N/A',
            'status' => $creditStatus,
            'lastTransaction' => $decryptedData['lastactive_credit'] ?? 'N/A'
        ]
    ];
}

// Procesar la solicitud para obtener información o cambiar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['get_info'])) {
        // Obtener la información de la tarjeta usando el número de tarjeta
        $card_number = $_POST['card_number']; // Ahora se usa el número de la tarjeta
        // Comprobamos si el número de tarjeta existe
        $card_info_error = "";
        if ($card_number === $decryptedData['card_debit'] || $card_number === $decryptedData['card_credit']) {
            // Ya tenemos la información de la tarjeta, no es necesario buscarla de nuevo
            $card_info = $decryptedData; 
        } else {
            $card_info_error = "No se encontró una tarjeta con ese número.";
        }
    } elseif (isset($_POST['enable_cards'])) {
        // Verificar cuál tarjeta se seleccionó para habilitar
      // Obtener el estado actual de la tarjeta activa (Débito o Crédito)
$currentCard = $decryptedData['current_card'] ?? '';

// Procesar solicitud POST para activar o desactivar tarjeta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enable_cards'])) {
        $card_type = $_POST['card_type']; // Tipo de tarjeta (DEBIT o CREDIT)
        
        // Realizar la solicitud a la API para activar/desactivar la tarjeta seleccionada
        $apiUrl = "http://localhost:4000/enablecard1/{$username}/{$card_type}";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($ch);
        if (curl_errno($ch)) {
            $apiResponseError = "Error de cURL: " . curl_error($ch);
        }
        curl_close($ch);
        
        // Suponiendo que la API maneja el cambio de estado, actualizar el valor de $currentCard
        // Dependiendo de la respuesta de la API, podemos cambiar el valor de $currentCard
        if ($currentCard === $card_type) {
            $currentCard = ''; // Desactivar la tarjeta si estaba activa
        } else {
            $currentCard = $card_type; // Activar la tarjeta seleccionada
        }
    }
}
        }
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tarjetas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

<style>
.card-selectable {
    border: 2px solid #ddd;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    cursor: pointer;
    position: relative;
    background-size: contain; /* Asegura que la imagen se ajuste al tamaño del div */
    background-position: center;
    background-repeat: no-repeat; /* Evita que la imagen se repita */
    width: 100%;  /* Ajusta el div al tamaño del contenedor, puedes cambiarlo si prefieres un tamaño fijo */
    height: auto;  /* El alto se ajusta según la imagen */
}

#debit-card {
    background-image: url('Debito.png');
    height: 250px; /* Ajusta la altura según el tamaño de la imagen */
}

#credit-card {
    background-image: url('Credito.png');
    height: 250px; /* Ajusta la altura según el tamaño de la imagen */
}

.card-selectable.active {
    background-color: rgba(255, 255, 255, 0.5); /* Fondo blanco trasparente */
    border-color: #28a745;  /* Borde verde */
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5); /* Sombra verde */
}

.card-selectable:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Estilo para el número de tarjeta alineado a la derecha */
.card-body {
    position: absolute;
    bottom: 20px; /* Ajuste la posición del contenido hacia la parte inferior */
    left: 10px; /* Espacio desde la izquierda */
    right: 10px; /* Espacio desde la derecha */
    text-align: right; /* Alineación del texto a la derecha */
}

.card-body .card-text {
    font-size: 1.2rem; /* Tamaño de fuente más grande si lo prefieres */
    font-weight: bold; /* Negrita */
}

    </style>
</head>
<body>

    <div class="container-fluid min-vh-100 py-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="h3">Gestión de Tarjetas</h1>
                <p class="lead">Activa, desactiva y consulta el estado de las tarjetas de tus colaboradores.</p>
            </div>
        </div>

        <section class="row mb-4">
            <div class="col-12 col-md-6 col-lg-4 mx-auto">
                <h3 class="h5">Consulta de Tarjetas</h3>
                <form id="card-query-form">
                    <div class="form-group">
                        <label for="card-number">Número de Tarjeta</label>
                        <input type="text" class="form-control" id="card-number" placeholder="Número de tarjeta" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Consultar Información</button>
                </form>
                <div id="card-info" class="mt-3 d-none">
                    <h5>Información de la Tarjeta</h5>
                    <p><strong>Número:</strong> <span id="card-info-number"></span></p>
                    <p><strong>Estado:</strong> <span id="card-info-status"></span></p>
                    <p><strong>Última Transacción:</strong> <span id="card-info-transaction"></span></p>
                </div>
            </div>
        </section>

        <section class="row mb-4 justify-content-center">
            <div class="card-deck col-md-8">
     <!-- Tarjeta de Débito -->
<div class="card card-selectable" id="debit-card" data-card-type="DEBIT">
    <div class="card-body">
        <h5 class="card-title">Tarjeta Débito</h5>
        <p class="card-text"><?php echo htmlspecialchars($cards[0]['number']); ?></p>
    </div>
</div>

<!-- Tarjeta de Crédito -->
<div class="card card-selectable" id="credit-card" data-card-type="CREDIT">
    <div class="card-body">
        <h5 class="card-title">Tarjeta Crédito</h5>
        <p class="card-text"><?php echo htmlspecialchars($cards[1]['number']); ?></p>
    </div>
</div>
    </section>

      



        <?php if (isset($message) && $message): ?>
            <div class="alert alert-warning text-center"><?php echo htmlspecialchars($message); ?></div>
        <?php elseif (isset($apiResponseError)): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($apiResponseError); ?></div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <h3 class="h5">Estado de las Tarjetas</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Número de Tarjeta</th>
                                    <th>Estado</th>
                                    <th>Última Transacción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cards as $card): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($card['number']); ?></td>
                                        <td><?php echo htmlspecialchars($card['status']); ?></td>
                                        <td><?php echo htmlspecialchars($card['lastTransaction']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <script>
            const cards = <?php echo json_encode($cards); ?>;
            function updateCardStatus(cardNumber) {
                alert("Función no implementada: Actualizar estado de " + cardNumber);
            }

            document.getElementById('card-query-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const cardNumber = document.getElementById('card-number').value.trim();
                const card = cards.find(c => c.number === cardNumber);

                if (card) {
                    document.getElementById('card-info').classList.remove('d-none');
                    document.getElementById('card-info-number').textContent = card.number;
                    document.getElementById('card-info-status').textContent = card.status;
                    document.getElementById('card-info-transaction').textContent = card.lastTransaction;
                } else {
                    alert('Tarjeta no encontrada.');
                    document.getElementById('card-info').classList.add('d-none');
                }
            });

        

            document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card-selectable');
    let activeCard = '<?php echo $currentCard; ?>'; // Tarjeta activa actual (DEBIT o CREDIT)

    // Marcar visualmente la tarjeta activa al cargar
    if (activeCard === 'DEBIT') {
        document.getElementById('debit-card').classList.add('active');
    } else if (activeCard === 'CREDIT') {
        document.getElementById('credit-card').classList.add('active');
    }

    // Evento de selección de tarjetas
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const cardType = card.dataset.cardType;

            // Verificar si la tarjeta seleccionada ya está activa
            if (activeCard === cardType) {
                alert(`La tarjeta ${cardType} ya está activa.`);
                return; // Salir sin hacer nada
            }

            // Realizar la solicitud para activar la nueva tarjeta
            fetch(`http://localhost:4000/enablecard1/<?php echo $username; ?>/${cardType}`, {
    method: 'GET'
})
.then(response => response.json())  // Asegúrate de que la respuesta esté en formato JSON
.then(data => {
    console.log(data);  // Agregar este log para depuración
    
       // alert(`Tarjeta ${cardType} activada correctamente.`);
        activeCard = cardType; // Actualizar el estado activo
        updateCardVisuals(card); // Actualizar visualmente
        updateCardStatusInTable(cardType); // Actualizar estado en la tabla
   
})

        });
    });

    // Función para actualizar la visualización de tarjetas activas
    function updateCardVisuals(selectedCard) {
        cards.forEach(card => card.classList.remove('active')); // Quitar la clase activa de todas las tarjetas
        selectedCard.classList.add('active'); // Activar la tarjeta seleccionada
  
    }

     // Función para actualizar el estado de la tarjeta en la tabla
     function updateCardStatusInTable(cardType) {
        const statusColumn = document.querySelectorAll('table tbody tr');
        statusColumn.forEach(row => {
            const cardNumber = row.cells[0].textContent.trim();
            if (cardNumber === "<?php echo $cards[0]['number']; ?>") {  // Tarjeta Débito
                row.cells[1].textContent = (cardType === 'DEBIT') ? 'Activo' : 'Inactivo';
            }
            if (cardNumber === "<?php echo $cards[1]['number']; ?>") {  // Tarjeta Crédito
                row.cells[1].textContent = (cardType === 'CREDIT') ? 'Activo' : 'Inactivo';
            }
        });
    }

   
});


   


        </script>
    </div>
</body>
</html>
