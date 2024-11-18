<?php
// Aseguramos que el usuario haya iniciado sesión correctamente, por ejemplo, mediante un token o sesión.
session_start();

// Verificamos si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // Redirigimos a la página de inicio de sesión si no hay sesión activa
    header("Location: index.php");
    exit();
}

// Si todo está bien, mostramos la página de bienvenida
$username = $_SESSION['username']; // Asumimos que el nombre de usuario se guarda en la sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card p-4 shadow">
                <h2 class="text-center mb-4">Bienvenido, <?php echo htmlspecialchars($username); ?>!</h2>
                <p class="text-center">Has iniciado sesión correctamente. ¡Ahora puedes acceder a las funcionalidades de tu cuenta!</p>
                <div class="text-center">
                    <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
