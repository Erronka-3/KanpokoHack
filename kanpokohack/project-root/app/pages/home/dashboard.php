<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Dashboard</title>
    <!-- Incluye Bootstrap desde un CDN -->
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
session_start();

include(__DIR__ . '/../../../config/config.php');
 
// // Verificar si hay roles en sesión
// if (!isset($_SESSION['user_roles'])) {
  
//     header('Location: \project-root\public\index.php');
    
// }
// Comprobar si hay un token de autenticación en la sesión
if (!isset($_SESSION['user_roles'])) {
    header("Location: index.php?route=6");
    exit;
    
}
include '../app/pages/menu/menu.php';
 
// Obtener roles
$userRoles = $_SESSION['user_roles'];
 
// Verificar si el usuario tiene el rol "admin"
$isAdmin = in_array('admin', $userRoles);
?>



<body>


    <!-- Contenedor principal -->
    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center py-3">

        <!-- Cabecera de bienvenida -->
        <header class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="h3">Bienvenido, <span id="user-name"><?php echo  $_SESSION['name']; ?></span></h1>
                <p class="lead">Rol: <strong
                        id="user-role"><?php echo $isAdmin ? 'Administrador' : 'Usuario'; ?></strong></p>
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
                    <a href="index.php?route=3" class="btn btn-primary mb-2 col-12 col-md-5 col-lg-2 mx-1">Tarjetas</a>
                    <a href="index.php?route=4" class="btn btn-secondary mb-2 col-12 col-md-5 col-lg-2 mx-1">Gastos</a>
                    <a href="index.php?route=13" class="btn btn-dark mb-2 col-12 col-md-5 col-lg-2 mx-1">Soporte</a>


                    <!-- Enlace visible para todos los usuarios -->
                    <a href="index.php?route=2" class="btn btn-warning mb-2 col-12 col-md-5 col-lg-2 mx-1">Mi cuenta</a>
                    <!-- Enlace a la página de modificar usuario -->


                </div>
            </div>
        </section>

        <!-- Vista Personalizada según Rol -->
        <section class="row">
            <div class="col-12 text-center">
                <h2 class="h5 mb-3">
                    Información de
                    <span id="role-specific">
                        <?php echo $isAdmin ? 'Administrador' : 'Usuario'; ?>
                    </span>
                </h2>

                <!-- Mostrar el botón de Administrador solo si el rol es Admin -->
                <?php if ($isAdmin): ?>
                <div class="d-flex justify-content-center">
                    <div class="dropdown">
                        <button class="btn btn-danger dropdown-toggle" type="button" id="adminDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Administrador
                        </button>
                        <div class="dropdown-menu" aria-labelledby="adminDropdown">
                            <a class="dropdown-item" href="index.php?route=12">Gestión de Usuarios</a>
                            <a class="dropdown-item" href="#web">Configuración del Sistema</a>
                            <a class="dropdown-item" href="#web">Revisión de Seguridad</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Información para usuarios no admins -->
                <?php if (!$isAdmin): ?>
                <div id="user-section" class="text-center">
                    <p>Panel de control para el Usuario. Incluye tus configuraciones personales y las opciones
                        disponibles.</p>
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