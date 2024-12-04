<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include(__DIR__ . '/../../../config/db/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $importe = $_POST['importe'];
    $ticket = $_FILES['ticket']['name'];

    // Subir archivo del ticket al servidor
    if (!empty($ticket)) {
        $target_dir = "storage/";
        $target_file = $target_dir . basename($ticket);
        move_uploaded_file($_FILES['ticket']['tmp_name'], $target_file);
    }

    // Insertar el gasto en la base de datos
    $sql = "INSERT INTO gastos (nombre, fecha, importe, ticket, usuario_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$descripcion, $fecha, $importe, $ticket, 1]); // Usuario 1 como ejemplo

    echo json_encode(['success' => true, 'message' => 'Gasto registrado correctamente.']);
}
?>
