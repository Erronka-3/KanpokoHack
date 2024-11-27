<?php
include 'db.php';

$sql = "SELECT id, fecha, nombre AS descripcion, importe, ticket FROM gastos ORDER BY fecha DESC";
$stmt = $pdo->query($sql);
$gastos = $stmt->fetchAll();

echo json_encode($gastos);
?>
