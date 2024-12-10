<?php
// get_expense_by_id.php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include(__DIR__ . '/../../../config/db/db.php');

session_start(); // Iniciar sesión para acceder al usuario

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?route=6");
    exit;
}

// Obtener el ID del usuario autenticado desde la sesión
$user_id = $_SESSION['user_id'];

// Verificar si se ha enviado el ID del gasto
if (isset($_GET['id'])) {
    $expenseId = $_GET['id'];
    
    // Preparar la consulta para obtener el gasto por ID
    $stmt = $pdo->prepare("SELECT id, fecha, nombre AS descripcion, importe, ticket, usuario 
                           FROM gastos 
                           WHERE usuario = ? AND id = ?");
    $stmt->execute([$user_id, $expenseId]);
    
    // Obtener los resultados
    $expense = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si no se encuentra el gasto, devolver un mensaje de error
    if ($expense) {
        echo json_encode($expense); // Devolver los detalles del gasto en formato JSON
    } else {
        echo json_encode(['error' => 'Expense not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
