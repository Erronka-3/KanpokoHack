<?php
// config.php
define('KEYCLOAK_URL', 'http://192.168.146.144:8080'); // URL de Keycloak
define('REALM', 'KanpokoHack'); // Nombre del realm
define('CLIENT_ID', 'prueba'); // ID del cliente
define('CLIENT_SECRET', 'vL512W94NUvOWHGsnRNUAHeslpHeBm0r'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'http://localhost/KanpokoHack/project-root/backend/phpPrueba/callback.php'); // URL de redirección después del login
define('REDIRECT_LOGOUT_URI', 'http://localhost/KanpokoHack/project-root/backend/phpPrueba/index.php?logout=true'); // URL de redirección después del login