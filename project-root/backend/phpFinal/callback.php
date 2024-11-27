<?php
include('config.php');
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
    $userInfoResponse = curl_exec($ch);
    curl_close($ch);

    if ($userInfoResponse) {
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
        header("Location: dashboard.php");
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
