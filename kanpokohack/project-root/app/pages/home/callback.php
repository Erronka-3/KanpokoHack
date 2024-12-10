<?php
ini_set('display_errors', '0'); // No mostrar errores en pantalla
ini_set('log_errors', '1');    // Registrar errores en un archivo
ini_set('error_log', __DIR__ . '/../../logs/error.log'); // Ruta al archivo de log
include(__DIR__ . '/../../../config/config.php');
session_start();

// Verificar si se ha recibido el código de autorización
if (!isset($_GET['code'])) {
    echo "Error: No se recibió el código de autorización.";
    exit();
}

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

// Realizar la solicitud para obtener el token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
// Rutas de los certificados
curl_setopt($ch, CURLOPT_SSLCERT, 'C:\\xampp\\htdocs\\RETO3_GRUPO\\project-root\\cert.pem'); // Certificado del cliente
curl_setopt($ch, CURLOPT_SSLKEY, 'C:\\xampp\\htdocs\\RETO3_GRUPO\\project-root\\key.pem'); // Clave privada del cliente
curl_setopt($ch, CURLOPT_CAINFO, 'C:\\xampp\\htdocs\\RETO3_GRUPO\\project-root\\cacert.pem'); // Certificado de la autoridad

// Verificación de los certificados SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifica el certificado del servidor
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Verifica el nombre del host del servidor

// Activar log de cURL
$logFile = 'curl_log.txt';
$fp = fopen($logFile, 'a');  // Abrir el archivo de log
curl_setopt($ch, CURLOPT_VERBOSE, true);  // Habilitar verbosidad
curl_setopt($ch, CURLOPT_STDERR, $fp);   // Redirigir el log a un archivo

// Realizar la solicitud
$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Verificar si la solicitud fue exitosa
if ($responseCode === 200) {
    $tokenData = json_decode($response, true);
    $_SESSION['access_token'] = $tokenData['access_token'];

    // Obtener la información del usuario
    $userInfoUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/userinfo";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $tokenData['access_token']
    ]);
    curl_setopt($ch, CURLOPT_SSLCERT, 'C:\xampp\htdocs\RETO3_GRUPO\kanpokohack\project-root\config\tls\cert.pem'); // Certificado del cliente
    curl_setopt($ch, CURLOPT_SSLKEY, 'C:\\xampp\\htdocs\\RETO3_GRUPO\\project-root\\key.pem'); // Clave privada del cliente
    curl_setopt($ch, CURLOPT_CAINFO, 'C:\\xampp\\htdocs\\RETO3_GRUPO\\project-root\\cacert.pem');

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifica el certificado del servidor
    
    $userInfoResponse = curl_exec($ch);
    $userInfoResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    if ($userInfoResponseCode === 200) {
        $userInfo = json_decode($userInfoResponse, true);
        
        // Almacenar la información del usuario en la sesión
        $_SESSION['name'] = $userInfo['name'] ?? null;
        $_SESSION['preferred_username'] = $userInfo['preferred_username'] ?? null;
        $_SESSION['user_id'] = $userInfo['sub'] ?? null;
        $_SESSION['first_name'] = $userInfo['given_name'] ?? null;
        $_SESSION['last_name'] = $userInfo['family_name'] ?? null;
        $_SESSION['email'] = $userInfo['email'] ?? null;

        // Decodificar el token para obtener los roles
        $decodedToken = decodeJwt($tokenData['access_token']);
        $roles = extractRolesFromToken($decodedToken);
        $accountRoles = extractAccountRoles($decodedToken);

        $_SESSION['user_roles'] = $roles;
        $_SESSION['account_roles'] = $accountRoles;

        // Verificar si el usuario tiene el rol "manage_account"
        $_SESSION['has_manage_account'] = in_array('manage-account', $accountRoles);

        // Redirigir al dashboard
        header("Location: ../../../public/index.php?route=1");
        exit();
    } else {
        echo "Error al obtener la información del usuario.";
        exit();
    }
} else {
    echo "Error al obtener el token: " . $response;
    exit();
}

// Función para decodificar el JWT
function decodeJwt($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception("El token no tiene un formato válido.");
    }
    return json_decode(base64_decode($parts[1]), true);
}

// Función para extraer los roles del token
function extractRolesFromToken($decodedToken) {
    $roles = [];

    if (isset($decodedToken['resource_access'][CLIENT_ID]['roles'])) {
        $roles = array_merge($roles, $decodedToken['resource_access'][CLIENT_ID]['roles']);
    }

    if (isset($decodedToken['realm_access']['roles'])) {
        $roles = array_merge($roles, $decodedToken['realm_access']['roles']);
    }

    return array_unique($roles);
}

// Función para extraer los roles de la cuenta
function extractAccountRoles($decodedToken) {
    return $decodedToken['resource_access']['account']['roles'] ?? [];
}
?>
