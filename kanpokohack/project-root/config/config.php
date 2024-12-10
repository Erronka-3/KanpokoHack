<?php
// config.php
define('KEYCLOAK_URL', 'https://192.168.44.145:8081'); // URL de Keycloak
define('REALM', 'KanpokoHack'); // Nombre del realm
define('CLIENT_ID', 'kanpokoHack'); // ID del cliente
define('CLIENT_SECRET', 'nQMvc7g4jdQMjUhOE63XJZMySJePvG72'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'https://localhost/Kanpokohack/Beñat/kanpokohack/project-root/app/pages/home/callback.php'); // URL de redirección después del login
define('REDIRECT_URI_LOGOUT', 'https://localhost/Kanpokohack/Beñat/kanpokohack/project-root/public/index.php');
?>