<?php
//update_expense.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?route=6");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include(__DIR__ . '/../../../config/db/db.php');

    $expenseId = $_POST['id'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $importe = $_POST['importe'] ?? null;
    $ticket = $_FILES['ticket']['name'] ?? null;

    if (!$expenseId || !$fecha || !$descripcion || !$importe) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    $fecha = htmlspecialchars($fecha);
    $descripcion = htmlspecialchars($descripcion);
    $importe = filter_var($importe, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $storage_dir = __DIR__ . '/storage/' . $user_id;

    if (!file_exists($storage_dir) && !mkdir($storage_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Error al crear el directorio de almacenamiento.']);
        exit;
    }

    try {
        if ($ticket) {
            $target_file = $storage_dir . '/' . basename($ticket);
            if (!move_uploaded_file($_FILES['ticket']['tmp_name'], $target_file)) {
                echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.']);
                exit;
            }
        } else {
            $stmt = $pdo->prepare("SELECT ticket FROM gastos WHERE id = ? AND usuario = ?");
            $stmt->execute([$expenseId, $user_id]);
            $ticket = $stmt->fetchColumn(); // Mantener el ticket actual si no se sube uno nuevo
        }

        $sql = "UPDATE gastos SET fecha = ?, nombre = ?, importe = ?, ticket = ? WHERE id = ? AND usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fecha, $descripcion, $importe, $ticket, $expenseId, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Gasto actualizado correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}
?>