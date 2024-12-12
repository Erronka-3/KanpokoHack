<?php
session_start();

// Verificar si se ha recibido el código de autorización
if (!isset($_SESSION['user_roles'])) {
    header("Location: index.php?route=6");
    exit;
    
}
// Obtener roles
$userRoles = $_SESSION['user_roles'];
 
// Verificar si el usuario tiene el rol "admin"
$isAdmin = in_array('admin', $userRoles);

if (!$isAdmin){

    header("Location: https://localhost/Kanpokohack/Beñat/kanpokohack/project-root/public/index.php");

}

?>


 
 
<a href="index.php?route=6" class="nav_link">
                <i class='bx bx-log-out nav_icon'></i>
                <span class="nav_name">SignOut</span>
</a>