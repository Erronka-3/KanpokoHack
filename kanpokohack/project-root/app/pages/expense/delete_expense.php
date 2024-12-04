<?php
// delete_expense.php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include(__DIR__ . '/../../../config/db/db.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Preparar la consulta para eliminar el gasto por ID
    $sql = "DELETE FROM gastos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);

    // Ejecutar la consulta y verificar si se eliminó correctamente
    if ($stmt->execute()) {
        echo "Gasto eliminado correctamente.";
    } else {
        echo "Hubo un error al eliminar el gasto.";
    }
} else {
    echo "No se ha recibido el ID del gasto.";
}
?>