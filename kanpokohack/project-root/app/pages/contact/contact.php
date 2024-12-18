<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../app/assets/css/styles_contact.css">
</head>
<?php
// Validación de envíos previos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'send_email.php';  // Procesar el formulario
}

ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
session_start();

include(__DIR__ . '/../../../config/config.php');

if (!isset($_SESSION['user_roles'])) {
    header("Location: index.php?route=6");
    exit;
}
include '../app/pages/menu/menu.php';
?>


<body>

    <div class="container">
        <div class="form-container">
            <h2>Formulario de Contacto</h2>

            <!-- Icono de la tuerca (configuración) -->
            <div class="intro-icon">
                <i class="fas fa-cogs"></i> <!-- Icono de la tuerca -->
            </div>

            <!-- Formulario de contacto -->
            <form action="index.php?route=14" method="POST" id="contact-form">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Asunto</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Mensaje</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn">Enviar Mensaje</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>