<?php
session_start();
include('../backend/phpPrueba/config.php');

// Verificar si hay roles en sesión
if (!isset($_SESSION['user_roles'])) {
    echo "No se pudo determinar el rol del usuario. Por favor, inicie sesión.";
    exit();
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
    <title>Panel de Usuario - Dashboard</title>
    <!-- Incluye Bootstrap desde un CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Archivo CSS personalizado -->
</head>
<body>

    <!-- Botón de logout -->
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">Dashboard</a>
        <form class="form-inline">
            <a href="/KanpokoHack/project-root/frontend/logout.php" class="btn btn-outline-danger">Logout</a>
            <!-- Enlace a cards.php -->
            <a href="cards.php" class="btn btn-outline-primary ml-2">Ir a Cards</a>
        </form>
    </nav>

    <!-- Contenedor principal -->
    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center py-3">
        
        <!-- Cabecera de bienvenida -->
        <header class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="h3">Bienvenido, <span id="user-name">Usuario</span></h1>
                <p class="lead">Rol: <strong id="user-role"><?php echo $isAdmin ? 'Administrador' : 'Usuario'; ?></strong></p>
            </div>
        </header>

        <!-- Sección de Resumen con tarjetas -->
        <section class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="card text-white bg-info h-100">
                    <div class="card-body">
                        <h5 class="card-title">Gastos Recientes</h5>
                        <p class="card-text">Resumen de los últimos gastos:</p>
                        <ul class="list-unstyled">
                            <li><strong>Gasto 1:</strong> $500</li>
                            <li><strong>Gasto 2:</strong> $200</li>
                            <li><strong>Gasto 3:</strong> $300</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Notificaciones</h5>
                        <p class="card-text">Mensajes importantes y alertas:</p>
                        <ul class="list-unstyled">
                            <li><strong>Notificación 1:</strong> Actualización disponible</li>
                            <li><strong>Notificación 2:</strong> Nuevo usuario registrado</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 mb-3">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body">
                        <h5 class="card-title">Estadísticas Generales</h5>
                        <p class="card-text">Vista rápida de estadísticas del sistema.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Accesos Rápidos -->
        <section class="row mb-4">
            <div class="col-12">
                <h2 class="h5 mb-3">Accesos Rápidos</h2>
                <div class="d-flex flex-wrap justify-content-around">
                    <button class="btn btn-primary mb-2 col-12 col-md-5 col-lg-2 mx-1">Usuarios</button>
                    <button class="btn btn-secondary mb-2 col-12 col-md-5 col-lg-2 mx-1">Reportes</button>
                    <button class="btn btn-info mb-2 col-12 col-md-5 col-lg-2 mx-1">Configuración</button>
                    <button class="btn btn-dark mb-2 col-12 col-md-5 col-lg-2 mx-1">Soporte</button>

                    <!-- Enlace visible para todos los usuarios -->
                    <a href="users.php" class="btn btn-warning mb-2 col-12 col-md-5 col-lg-2 mx-1">Modificar mi cuenta</a> <!-- Enlace a la página de modificar usuario -->

                    <!-- Mostrar el botón de Administrador solo si el rol es Admin -->
                    <?php if ($isAdmin): ?>
                        <div class="dropdown mb-2 col-12 col-md-5 col-lg-2 mx-1">
                            <button class="btn btn-danger dropdown-toggle" type="button" id="adminDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Administrador
                            </button>
                            <div class="dropdown-menu" aria-labelledby="adminDropdown">
                                <a class="dropdown-item" href="admin_users.php">Gestión de Usuarios</a> <!-- Enlace a la página de administración de usuarios -->
                                <a class="dropdown-item" href="#">Configuración del Sistema</a>
                                <a class="dropdown-item" href="#">Revisión de Seguridad</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Vista Personalizada según Rol -->
        <section class="row">
            <div class="col-12">
                <h2 class="h5 mb-3">Información de <span id="role-specific"><?php echo $isAdmin ? 'Administrador' : 'Usuario'; ?></span></h2>
                
                <!-- Información específica para el Administrador -->
                <?php if ($isAdmin): ?>
                    <div id="admin-section" style="display: none;">
                        <p>Panel de control para el Administrador. Incluye la configuración general y la gestión de usuarios.</p>
                        <ul class="list-group">
                            <li class="list-group-item">Gestión de Usuarios</li>
                            <li class="list-group-item">Configuración del Sistema</li>
                            <li class="list-group-item">Revisión de Seguridad</li>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Información para usuarios no admins -->
                <?php if (!$isAdmin): ?>
                    <div id="user-section">
                        <p>Panel de control para el Usuario. Incluye tus configuraciones personales y las opciones disponibles.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- JS de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/dashboard.js"></script> <!-- JS personalizado para roles -->
 
</body>
</html>
