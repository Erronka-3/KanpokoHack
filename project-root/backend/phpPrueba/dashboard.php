<?php
session_start();  // Inicia la sesión

// Verifica si el usuario está autenticado (si hay datos en la sesión)
if (!isset($_SESSION['user'])) {
    // Si no hay datos en la sesión, redirige al login
    header('Location: keycloak.php');
    exit;
}

$user = $_SESSION['user'];  // Obtiene los datos del usuario desde la sesión

// Función para sanitizar datos y prevenir XSS (inyección de código HTML/JS)
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bienvenido</title>
    <link rel="stylesheet" href="styles.css"> <!-- Agrega un archivo CSS para estilos -->
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo sanitize($user['name']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="profile.php">Perfil</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2>Información del Usuario</h2>
        <table>
            <tr>
                <th>Nombre</th>
                <td><?php echo sanitize($user['name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo sanitize($user['email']); ?></td>
            </tr>
            <tr>
                <th>Correo Verificado</th>
                <td><?php echo sanitize($user['email_verified'] ? 'Sí' : 'No'); ?></td>
            </tr>
            <!-- Agregar más campos dependiendo de la información disponible en el usuario -->
        </table>

        <h3>Roles</h3>
        <ul>
            <?php
            // Si el usuario tiene roles definidos, mostrar una lista
            if (isset($user['roles']) && is_array($user['roles'])) {
                foreach ($user['roles'] as $role) {
                    echo '<li>' . sanitize($role) . '</li>';
                }
            } else {
                echo '<li>No se encontraron roles asignados</li>';
            }
            ?>
        </ul>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Mi Aplicación</p>
    </footer>
</body>
</html>
