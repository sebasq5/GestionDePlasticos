<?php
/**
 * Cierra la sesión del usuario y redirige al login
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Mensaje de despedida
session_start();
$_SESSION['mensaje'] = 'Sesión cerrada exitosamente';
$_SESSION['tipo_mensaje'] = 'success';

// Redirigir al login
header('Location: /plasticos/views/login.php');
exit;
?>
