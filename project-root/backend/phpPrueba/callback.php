<?php
include('config.php');
session_start();

if (!isset($_GET['code'])) {
    echo "Error: No se recibió el código de autorización.";
    exit();
}

$authCode = $_GET['code'];

$tokenUrl = KEYCLOAK_URL . "/realms/" . REALM . "/protocol/openid-connect/token";
$postData = [
    'grant_type' => 'authorization_code',
    'code' => $authCode,
    'redirect_uri' => REDIRECT_URI,
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response = curl_exec($ch);
$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseCode === 200) {
    $tokenData = json_decode($response, true);
    $_SESSION['access_token'] = $tokenData['access_token'];

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
        $_SESSION['user'] = $userInfo['preferred_username'];
    }

    // Decodificar el token para obtener roles
    $decodedToken = decodeJwt($tokenData['access_token']);
    $roles = extractRolesFromToken($decodedToken);
    $accountRoles = extractAccountRoles($decodedToken);

    $_SESSION['user_roles'] = $roles;
    $_SESSION['account_roles'] = $accountRoles;

    // Verificar y guardar si tiene el rol "manage_account"
    if (in_array('manage-account', $accountRoles)) {
        $_SESSION['has_manage_account'] = true;
    } else {
        $_SESSION['has_manage_account'] = false;
    }

    header("Location: /KanpokoHack/project-root/frontend/dashboard.php");
    exit();
} else {
    echo "Error al obtener el token: " . $response;
    exit();
}

function decodeJwt($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception("El token no tiene un formato válido.");
    }
    return json_decode(base64_decode($parts[1]), true);
}

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

function extractAccountRoles($decodedToken) {
    return $decodedToken['resource_access']['account']['roles'] ?? [];
}
?>
