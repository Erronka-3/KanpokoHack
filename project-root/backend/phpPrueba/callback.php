<?php
 session_start();
include 'menu.php';
include('config.php');

try {
   
    // Verificar si Keycloak ha enviado el código de autorización
    if (isset($_GET['code'])) {
        $authCode = $_GET['code'];

        // URL para obtener el token
        $tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => REDIRECT_URI,
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET
        ];

        
        // Configurar y ejecutar cURL para obtener el token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verificar si la solicitud fue exitosa
        if ($responseCode == 200) {
            $tokenData = json_decode($response, true);
            $accessToken = $tokenData['access_token'];
            $idToken = $tokenData['id_token']; // Token para el logout

            // Decodificar el token JWT (información contenida en el payload)
            $parts = explode('.', $accessToken);
            if (count($parts) === 3) {
                list($header, $payload, $signature) = $parts;

                // Decodificar Base64 del payload
                $decodedPayload = json_decode(base64_decode($payload), true);

                // Mostrar nombre de usuario y almacenar en sesión
                displayUserName($decodedPayload);

                // Guardar el nombre y preferred_username en la sesión
                
                $_SESSION['access_token'] = $accessToken;
                $_SESSION['name'] = $decodedPayload['name']; // Nombre del usuario
                $_SESSION['preferred_username'] = $decodedPayload['preferred_username']; // Nombre de usuario preferido
                $_SESSION['user_id'] = $decodedPayload['sub'];
                $_SESSION['first_name'] = $decodedPayload['given_name'] ?? null;
                $_SESSION['last_name'] = $decodedPayload['family_name'] ?? null;
                $_SESSION['email'] = $decodedPayload['email'] ?? null;
                // Mostrar la información completa del usuario
                echo "<h1>Información del Usuario</h1>";
                echo "<pre>" . json_encode($decodedPayload, JSON_PRETTY_PRINT) . "</pre>";

                // Verificar si el usuario tiene el rol "Admin"
                if (isset($decodedPayload['realm_access']['roles']) && in_array('Admin', $decodedPayload['realm_access']['roles'])) {
                    echo "<h2>Información Exclusiva para Admin:</h2>";
                    echo "<p>Bienvenido, Admin. Aquí está la información confidencial.</p>";
                } else {
                    echo "<h2>Acceso Restringido:</h2>";
                    echo "<p>No tienes permisos para ver esta información.</p>";
                }

                // Guardar el ID Token en la sesión para usarlo en el logout
                $_SESSION['id_token'] = $idToken;

                // Mostrar el enlace de logout que redirige al archivo logout.php
                echo "<a href='logout.php'>Cerrar sesión</a>";

                // Agregar el botón que lleva a cards.php
                echo "<br><br><a href='cards.php'><button>Ver Datos de Tarjeta</button></a>";
            } else {
                echo "<p>Error: el token no parece ser un JWT válido.</p>";
            }
        } else {
            echo "<p>Error al obtener el token: " . htmlspecialchars($response) . "</p>";
        }
    } else {
        echo "<p>Error: no se recibió código de autorización.</p>";
    }
} catch (Exception $e) {
    echo "<p>Ocurrió un error inesperado: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Función para mostrar solo el nombre del usuario
function displayUserName($userData) {
    // Verificar que el nombre esté presente en los datos
    if (isset($userData['name']) && isset($userData['preferred_username'])) {
        echo "<h1>Información del Usuario</h1>";
        echo "<p><strong>Nombre:</strong> " . htmlspecialchars($userData['name']) . "</p>";
        echo "<p><strong>Nombre de Usuario:</strong> " . htmlspecialchars($userData['preferred_username']) . "</p>";
    } else {
        echo "<p>No se pudo obtener el nombre del usuario o el nombre de usuario preferido.</p>";
    }
}
?>
