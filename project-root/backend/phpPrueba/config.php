<?php
// config.php
define('KEYCLOAK_URL', 'http://192.168.101.133:8080'); // URL de Keycloak
define('REALM', 'MiAplicacionRealm'); // Nombre del realm
define('CLIENT_ID', 'public_client'); // ID del cliente
define('CLIENT_SECRET', 's8cpdFbuFc1AY9YnRYzhfx0Cevvm4Rj3'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'http://localhost/KanpokoHack/project-root/backend/phpPrueba/callback.php');
define('REDIRECT_LOGOUT_URI', 'http://localhost/KanpokoHack/project-root/backend/phpPrueba/index.php'); // URL de redirección después del login