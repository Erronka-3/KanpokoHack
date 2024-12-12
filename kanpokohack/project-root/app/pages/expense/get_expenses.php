<?php
// get_expenses.php
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
$userRoles = $_SESSION['user_roles'];
$isAdmin = in_array('admin', $userRoles); // Verificar si el usuario tiene el rol 'admin'

// Obtener las fechas y el rango de importes del filtro (si existen)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$min_amount = isset($_GET['min_amount']) ? $_GET['min_amount'] : '';
$max_amount = isset($_GET['max_amount']) ? $_GET['max_amount'] : '';

// Formatear las fechas correctamente (si están definidas)
if (!empty($start_date)) {
    $start_date = date('Y-m-d', strtotime($start_date));
}
if (!empty($end_date)) {
    $end_date = date('Y-m-d', strtotime($end_date));
}

// Validar los importes (asegurarse de que son numéricos)
if (!empty($min_amount) && is_numeric($min_amount)) {
    $min_amount = (float) $min_amount;
}
if (!empty($max_amount) && is_numeric($max_amount)) {
    $max_amount = (float) $max_amount;
}

// Construir la consulta SQL
$sql = "SELECT id, fecha, nombre AS descripcion, importe, ticket, usuario FROM gastos";
$conditions = []; // Condiciones para WHERE

if (!$isAdmin) {
    $conditions[] = "usuario = ?";
}

// Filtros de fechas
if (!empty($start_date) && !empty($end_date)) {
    $conditions[] = "fecha BETWEEN ? AND ?";
} elseif (!empty($start_date)) {
    $conditions[] = "fecha >= ?";
} elseif (!empty($end_date)) {
    $conditions[] = "fecha <= ?";
}

// Filtro de importes
if (!empty($min_amount) && !empty($max_amount)) {
    $conditions[] = "importe BETWEEN ? AND ?";
} elseif (!empty($min_amount)) {
    $conditions[] = "importe >= ?";
} elseif (!empty($max_amount)) {
    $conditions[] = "importe <= ?";
}

// Añadir las condiciones a la consulta SQL
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Ordenar por fecha descendente
$sql .= " ORDER BY fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $pdo->prepare($sql);

// Parámetros de la consulta
$params = [];
if (!$isAdmin) {
    $params[] = $user_id; // Solo si no es admin, se pasa el ID del usuario
}
if (!empty($start_date) && !empty($end_date)) {
    $params[] = $start_date;
    $params[] = $end_date;
} elseif (!empty($start_date)) {
    $params[] = $start_date;
} elseif (!empty($end_date)) {
    $params[] = $end_date;
}

if (!empty($min_amount) && !empty($max_amount)) {
    $params[] = $min_amount;
    $params[] = $max_amount;
} elseif (!empty($min_amount)) {
    $params[] = $min_amount;
} elseif (!empty($max_amount)) {
    $params[] = $max_amount;
}

// Registrar consulta para depuración
error_log("SQL Query: $sql");
error_log("Params: " . json_encode($params));

$stmt->execute($params);

$gastos = $stmt->fetchAll();

// Codificar correctamente los resultados para evitar problemas con caracteres especiales
foreach ($gastos as &$gasto) {
    $gasto = array_map('utf8_encode', $gasto);
}

// Registrar los datos para depuración
error_log(json_encode($gastos));

// Devolver los datos en formato JSON
echo json_encode($gastos);
?>
