<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y obtener datos del formulario
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validación básica del correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido.");
    }

    // Dirección de correo a la que se enviará el mensaje
    $to = "hodei.olivas@maristak.net";  // Aquí pon la dirección de correo que recibirá el mensaje

    // Cabeceras del correo
    $headers = "From: $name <$email>" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Intentar enviar el correo
    $mail_sent = mail($to, $subject, $message, $headers);

    if ($mail_sent) {
        echo "El mensaje ha sido enviado correctamente.";
    } else {
        // Verificar si error_get_last() devuelve algo antes de acceder al índice
        $last_error = error_get_last();
        if ($last_error) {
            error_log("Error al enviar el mensaje: " . $last_error['message']);
        }
        echo "Hubo un error al enviar el mensaje. Intenta nuevamente.";
    }
}
?>