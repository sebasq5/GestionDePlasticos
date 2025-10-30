<?php
/**
 * Archivo principal del sistema
 * Maneja el enrutamiento y control de acceso
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

session_start();

require_once __DIR__ . '/controllers/UsuarioController.php';

// Instanciar controlador de usuario
$usuarioController = new UsuarioController();

// Procesar login si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['usuario_id'])) {
    $correo = $_POST['correo'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    
    $resultado = $usuarioController->login($correo, $contraseña);
    
    if ($resultado['exito']) {
        $_SESSION['mensaje'] = $resultado['mensaje'];
        $_SESSION['tipo_mensaje'] = 'success';
        // Redirigir según el rol
        header('Location: /plasticos/index.php');
        exit;
    } else {
        $_SESSION['mensaje'] = $resultado['mensaje'];
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: /plasticos/views/login.php');
        exit;
    }
}

// Si no hay sesión, mostrar login
if (!$usuarioController->verificarSesion()) {
    header('Location: /plasticos/views/login.php');
    exit;
}

// Redirigir al dashboard según el rol
$rol = $_SESSION['usuario_rol'];

switch ($rol) {
    case 'admin':
        header('Location: /plasticos/views/dashboard_admin.php');
        break;
    case 'empleado':
        header('Location: /plasticos/views/dashboard_empleado.php');
        break;
    case 'proveedor':
        header('Location: /plasticos/views/dashboard_proveedor.php');
        break;
    default:
        // Rol no reconocido, cerrar sesión
        header('Location: /plasticos/logout.php');
        exit;
}
?>
