<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $importe = $_POST['importe'];
    $ticket = $_FILES['ticket']['name'];

    // Subir archivo del ticket al servidor
    if (!empty($ticket)) {
        $target_dir = "uploads/";
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
