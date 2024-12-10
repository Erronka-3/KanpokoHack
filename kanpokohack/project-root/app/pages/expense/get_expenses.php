<?php
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

$sql = "SELECT id, fecha, nombre AS descripcion, importe, ticket, usuario FROM gastos WHERE usuario = ? ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$gastos = $stmt->fetchAll();
error_log(json_encode($gastos));
echo json_encode($gastos);
?>