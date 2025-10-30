<?php
/**
 * Funciones y componentes reutilizables para las vistas
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

/**
 * Genera el encabezado HTML común para todas las páginas
 * 
 * @param string $titulo Título de la página
 * @param string $descripcion Descripción opcional
 */
function generarEncabezado($titulo = "Sistema de Gestión de Plásticos", $descripcion = "") {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($titulo); ?></title>
        <?php if ($descripcion): ?>
        <meta name="description" content="<?php echo htmlspecialchars($descripcion); ?>">
        <?php endif; ?>
        
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Estilos personalizados -->
        <link rel="stylesheet" href="/plasticos/assets/css/estilos.css">
    </head>
    <body>
    <?php
}

/**
 * Genera la barra de navegación
 * 
 * @param string $rolUsuario Rol del usuario actual
 */
function generarNavegacion($rolUsuario) {
    $nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
    $correoUsuario = $_SESSION['usuario_correo'] ?? '';
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/plasticos/index.php">
                <i class="fas fa-recycle"></i> Gestión de Plásticos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($rolUsuario === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/dashboard_admin.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/usuarios/listar.php">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/proveedores/listar.php">
                                <i class="fas fa-building"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/compras/listar.php">
                                <i class="fas fa-shopping-cart"></i> Compras
                            </a>
                        </li>
                    <?php elseif ($rolUsuario === 'empleado'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/dashboard_empleado.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/proveedores/listar.php">
                                <i class="fas fa-building"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/compras/listar.php">
                                <i class="fas fa-shopping-cart"></i> Compras
                            </a>
                        </li>
                    <?php elseif ($rolUsuario === 'proveedor'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/dashboard_proveedor.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plasticos/views/compras/listar.php">
                                <i class="fas fa-shopping-cart"></i> Mis Compras
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($nombreUsuario); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><h6 class="dropdown-header"><?php echo htmlspecialchars($correoUsuario); ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><span class="dropdown-item-text"><strong>Rol:</strong> <?php echo ucfirst($rolUsuario); ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/plasticos/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Genera el pie de página HTML
 */
function generarPiePagina() {
    ?>
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="text-muted mb-0">
                <i class="fas fa-recycle"></i> Sistema de Gestión de Plásticos
                | Desarrollado por Sebastian Sanmartin - Aivra
            </p>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts personalizados -->
    <script src="/plasticos/assets/js/scripts.js"></script>
    </body>
    </html>
    <?php
}

/**
 * Muestra un mensaje de alerta
 * 
 * @param string $mensaje Mensaje a mostrar
 * @param string $tipo Tipo de alerta (success, danger, warning, info)
 */
function mostrarAlerta($mensaje, $tipo = 'info') {
    $icono = [
        'success' => 'check-circle',
        'danger' => 'exclamation-triangle',
        'warning' => 'exclamation-circle',
        'info' => 'info-circle'
    ][$tipo] ?? 'info-circle';
    
    echo '<div class="alert alert-' . $tipo . ' alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-' . $icono . '"></i> ' . htmlspecialchars($mensaje);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
}

/**
 * Verifica permisos de acceso según el rol
 * 
 * @param array $rolesPermitidos Array de roles permitidos
 */
function verificarAcceso($rolesPermitidos = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar si hay sesión activa
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /plasticos/views/login.php');
        exit;
    }
    
    // Verificar rol si se especifican roles permitidos
    if (!empty($rolesPermitidos) && !in_array($_SESSION['usuario_rol'], $rolesPermitidos)) {
        $_SESSION['mensaje'] = 'No tiene permisos para acceder a esta sección';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: /plasticos/index.php');
        exit;
    }
}

/**
 * Formatea un número como moneda
 * 
 * @param float $valor Valor a formatear
 * @return string Valor formateado
 */
function formatearMoneda($valor) {
    return '$' . number_format($valor, 2, '.', ',');
}

/**
 * Formatea una fecha
 * 
 * @param string $fecha Fecha a formatear
 * @param string $formato Formato de salida
 * @return string Fecha formateada
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    return date($formato, strtotime($fecha));
}
?>
