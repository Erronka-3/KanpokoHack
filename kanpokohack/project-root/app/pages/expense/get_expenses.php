<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include(__DIR__ . '/../../../config/db/db.php');

$sql = "SELECT id, fecha, nombre AS descripcion, importe, ticket FROM gastos ORDER BY fecha DESC";
$stmt = $pdo->query($sql);
$gastos = $stmt->fetchAll();

echo json_encode($gastos);
?>
