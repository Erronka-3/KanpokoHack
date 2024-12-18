<?php
// Configurar el manejo de errores
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../../logs/error.log'); // Ruta al archivo de log

include(__DIR__ . '/../../../config/db/db.php'); // Configuración de la base de datos

session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?route=6");
    exit;
}

// Obtener el ID del usuario autenticado
$user_id = $_SESSION['user_id'];

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y obtener datos del formulario
    $fecha = $_POST['fecha'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $importe = $_POST['importe'] ?? null;
    $ticket = $_FILES['ticket']['name'] ?? null;
    
    // Validar los campos requeridos
    if (!$fecha || !$descripcion || !$importe || !$ticket) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    // Ruta base del directorio de almacenamiento
    $storage_dir = __DIR__ . '/storage/' . $user_id;

    // Crear el directorio del usuario si no existe
    if (!file_exists($storage_dir)) {
        if (!mkdir($storage_dir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'Error al crear el directorio de almacenamiento.']);
            exit;
        }
    }

    // Ruta completa del archivo destino
    $target_file = $storage_dir . '/' . basename($ticket);

    // Mover el archivo subido al directorio correspondiente
    if (move_uploaded_file($_FILES['ticket']['tmp_name'], $target_file)) {
        // Guardar los datos en la base de datos
        $sql = "INSERT INTO gastos (nombre, fecha, importe, ticket, usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$descripcion, $fecha, $importe, $ticket, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Gasto registrado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.']);
    }
}
?>