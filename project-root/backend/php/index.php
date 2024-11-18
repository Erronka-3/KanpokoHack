<?php
session_start();

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Configurar los parámetros de conexión LDAP
    $ldap_server = "ldap://192.168.44.144"; // Dirección IP del servidor LDAP
    $ldap_dn = "dc=kanpokohack,dc=com"; // Dominio de búsqueda
    $ldap_user = "cn=admin,dc=kanpokohack,dc=com"; // Usuario con permisos para consultar LDAP

    // Crear conexión LDAP
    $ldap_conn = ldap_connect($ldap_server);

    if (!$ldap_conn) {
        echo "No se pudo conectar al servidor LDAP.";
        exit;
    }

    // Establecer el protocolo LDAP
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

    // Verificar las credenciales del admin
    $bind = ldap_bind($ldap_conn, $ldap_user, 'KanpokoHack'); // Aquí pon la contraseña del usuario admin

    if ($bind) {
        // Buscar el usuario en el LDAP
        $search = ldap_search($ldap_conn, $ldap_dn, "(uid=$username)");

        // Obtener los resultados
        $result = ldap_get_entries($ldap_conn, $search);

        if ($result['count'] > 0) {
            // El usuario fue encontrado en el LDAP, verificar la contraseña
            $user_dn = $result[0]['dn'];

            // Intentar hacer un bind con las credenciales del usuario
            $user_bind = ldap_bind($ldap_conn, $user_dn, $password);

            if ($user_bind) {
                // Autenticación exitosa
                $_SESSION['username'] = $username;
                header("Location: welcome.php"); // Redirigir a una página de bienvenida
                exit;
            } else {
                // Error al hacer bind con las credenciales del usuario
                $error_message = "Credenciales incorrectas.";
            }
        } else {
            // Usuario no encontrado en LDAP
            $error_message = "Usuario no encontrado.";
        }
    } else {
        // Error al hacer bind con el admin
        $error_message = "No se pudo conectar al servidor LDAP con el usuario admin. Error: " . ldap_error($ldap_conn);
    }

    // Cerrar la conexión LDAP
    ldap_unbind($ldap_conn);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Incluye el CSS de Bootstrap desde un CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="login-container card p-4 shadow">
                <h2 class="text-center mb-4">Iniciar sesión</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                </form>
                <?php
                if (isset($error_message)) {
                    echo '<div class="text-danger mt-3">' . $error_message . '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Incluye el JS de Bootstrap desde un CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
