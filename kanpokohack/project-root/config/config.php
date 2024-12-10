<?php
// config.php
define('KEYCLOAK_URL', 'https://192.168.101.133:8081'); // URL de Keycloak
define('REALM', 'ldap'); // Nombre del realm
define('CLIENT_ID', 'public_client'); // ID del cliente
define('CLIENT_SECRET', '691cCXI2BMLDuEJBYfUu9SDC9tsN8wIR'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'https://localhost/RETO3_GRUPO/kanpokohack/project-root/app/pages/home/callback.php'); // URL de redirección después del login
define('REDIRECT_URI_LOGOUT', 'https://localhost/RETO3_GRUPO/kanpokohack/project-root/public/index.php');
?>