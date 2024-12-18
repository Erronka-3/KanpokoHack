<?php
#session_start();
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log


include(__DIR__ . '/../../../config/config.php');

// Verificar si hay un token de autenticación en la sesión
if (!isset($_SESSION['user_roles'])) {
    header("Location: index.php?route=6");
    exit;
}

// Obtener roles
$userRoles = $_SESSION['user_roles'];

// Verificar si el usuario tiene el rol "admin"
$isAdmin = in_array('admin', $userRoles);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Lateral Moderno</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons para iconos -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../app/assets/css/styles_menu.css">
    <script defer src="../app/assets/js/scripts_menu.js"></script>

</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>

    </header>

    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="index.php?route=1" class="nav_logo">
                    <i class='bx bx-layer nav_logo-icon'></i>
                    <span class="nav_logo-name">KanpokoHack</span>
                </a>
                <div class="nav_list">
                    <a href="index.php?route=1" class="nav_link">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="index.php?route=2" class="nav_link">
                        <i class='bx bx-user nav_icon'></i>
                        <span class="nav_name">Profile</span>
                    </a>

                    <a href="index.php?route=3" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Cards</span>
                    </a>
                    <a href="index.php?route=4" class="nav_link">
                        <i class='bx bx-bar-chart-alt-2 nav_icon'></i>
                        <span class="nav_name">Expense</span>
                    </a>
                    <a href="index.php?route=13" class="nav_link">
                        <i class='bx bx-message-square-detail nav_icon'></i>
                        <span class="nav_name">Contact</span>
                    </a>
                    <!-- Ruta 12 (Mensajes) bloqueada si no es admin -->
                    <a href="index.php?route=12" class="nav_link <?php echo !$isAdmin ? 'blocked' : ''; ?>"
                        <?php echo !$isAdmin ? 'aria-disabled="true"' : ''; ?>>
                        <i class='bx bx-cog nav_icon'></i>
                        <span class="nav_name">Settings</span>
                    </a>

                </div>
            </div>
            <a href="index.php?route=6" class="nav_link">
                <i class='bx bx-log-out nav_icon'></i>
                <span class="nav_name">SignOut</span>
            </a>
        </nav>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>