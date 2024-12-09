<?php
// config.php
define('KEYCLOAK_URL', 'http://192.168.44.145:8080'); // URL de Keycloak
define('REALM', 'KanpokoHack'); // Nombre del realm
define('CLIENT_ID', 'confidential-client'); // ID del cliente
define('CLIENT_SECRET', '6Hq8MaahVitpQY9tqXp3xIeGE1v9MNYF'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'http://localhost/Kanpokohack/kanpokohack/project-root/app/pages/home/callback.php'); // URL de redirección después del login
define('REDIRECT_URI_LOGOUT', 'http://localhost/Kanpokohack/kanpokohack/project-root/public/index.php');
?>