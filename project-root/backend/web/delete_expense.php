<?php
// delete_expense.php

include 'db.php'; // Asegúrate de que la ruta del archivo de conexión es correcta

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