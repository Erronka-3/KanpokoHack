<?php
// config.php

//Conexiones Keycloak
define('KEYCLOAK_URL', 'https://192.168.44.145:8081'); // URL de Keycloak
define('REALM', 'KanpokoHack'); // Nombre del realm
define('CLIENT_ID', 'security'); // ID del cliente
define('CLIENT_SECRET', 'RiF2gUB5FLWhYE7eu6z89neD9XLO7b2C'); // (Solo si es un cliente confidencial)
define('REDIRECT_URI', 'https://localhost/Kanpokohack/Beñat/security/project-root/pages/callback.php'); // URL de redirección después del login
define('REDIRECT_URI_LOGOUT', 'https://localhost/Kanpokohack/Beñat/security/project-root/pages/index.php');

//Certificados
define('CERT', 'C:\xampp\htdocs\KanpokoHack\Beñat\security\project-root\config\tls\cert.pem');
define('KEY', 'C:\xampp\htdocs\KanpokoHack\Beñat\security\project-root\config\tls\key.pem');
define('CACERT', 'C:\xampp\htdocs\KanpokoHack\Beñat\security\project-root\config\tls\cacert.pem');


?>