<?php
/**
 * Script de Prueba de Conexión
 * 
 * Este archivo verifica que la conexión a la base de datos funcione correctamente
 * Debe eliminarse o protegerse en producción
 * 
 * Acceso: http://localhost/plasticos/test_conexion.php
 */

// Configurar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión - Sistema de Plásticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .test-card { max-width: 800px; margin: 50px auto; }
        .check-item { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .check-success { background-color: #d4edda; color: #155724; }
        .check-error { background-color: #f8d7da; color: #721c24; }
        .check-warning { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-stethoscope"></i> Diagnóstico del Sistema</h4>
                </div>
                <div class="card-body">
                    <h5>🔍 Verificando Configuración...</h5>
                    <hr>
                    
                    <?php
                    $errores = 0;
                    $advertencias = 0;
                    
                    // 1. Verificar versión de PHP
                    echo '<div class="check-item ';
                    if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
                        echo 'check-success"><strong>✓ PHP Version:</strong> ' . PHP_VERSION . ' (Compatible)';
                    } else {
                        echo 'check-error"><strong>✗ PHP Version:</strong> ' . PHP_VERSION . ' (Se requiere PHP 8.0+)';
                        $errores++;
                    }
                    echo '</div>';
                    
                    // 2. Verificar extensión PDO
                    echo '<div class="check-item ';
                    if (extension_loaded('pdo')) {
                        echo 'check-success"><strong>✓ PDO:</strong> Instalado';
                    } else {
                        echo 'check-error"><strong>✗ PDO:</strong> No instalado';
                        $errores++;
                    }
                    echo '</div>';
                    
                    // 3. Verificar PDO MySQL
                    echo '<div class="check-item ';
                    if (extension_loaded('pdo_mysql')) {
                        echo 'check-success"><strong>✓ PDO MySQL:</strong> Instalado';
                    } else {
                        echo 'check-error"><strong>✗ PDO MySQL:</strong> No instalado';
                        $errores++;
                    }
                    echo '</div>';
                    
                    // 4. Verificar mbstring
                    echo '<div class="check-item ';
                    if (extension_loaded('mbstring')) {
                        echo 'check-success"><strong>✓ mbstring:</strong> Instalado';
                    } else {
                        echo 'check-warning"><strong>⚠ mbstring:</strong> No instalado (recomendado)';
                        $advertencias++;
                    }
                    echo '</div>';
                    
                    // 5. Verificar archivo de configuración
                    echo '<div class="check-item ';
                    if (file_exists(__DIR__ . '/config/conexion.php')) {
                        echo 'check-success"><strong>✓ Archivo de configuración:</strong> Encontrado';
                    } else {
                        echo 'check-error"><strong>✗ Archivo de configuración:</strong> No encontrado';
                        $errores++;
                    }
                    echo '</div>';
                    
                    // 6. Probar conexión a base de datos
                    if (file_exists(__DIR__ . '/config/conexion.php')) {
                        try {
                            require_once __DIR__ . '/config/conexion.php';
                            $db = getDB();
                            
                            echo '<div class="check-item check-success">';
                            echo '<strong>✓ Conexión a Base de Datos:</strong> Exitosa';
                            echo '</div>';
                            
                            // Verificar tablas
                            $tablas = ['usuarios', 'proveedores', 'compras'];
                            foreach ($tablas as $tabla) {
                                $stmt = $db->query("SHOW TABLES LIKE '$tabla'");
                                echo '<div class="check-item ';
                                if ($stmt->rowCount() > 0) {
                                    echo 'check-success"><strong>✓ Tabla "$tabla":</strong> Existe';
                                } else {
                                    echo 'check-error"><strong>✗ Tabla "$tabla":</strong> No existe';
                                    $errores++;
                                }
                                echo '</div>';
                            }
                            
                            // Contar registros
                            $stmt = $db->query("SELECT COUNT(*) FROM usuarios");
                            $totalUsuarios = $stmt->fetchColumn();
                            
                            $stmt = $db->query("SELECT COUNT(*) FROM proveedores");
                            $totalProveedores = $stmt->fetchColumn();
                            
                            $stmt = $db->query("SELECT COUNT(*) FROM compras");
                            $totalCompras = $stmt->fetchColumn();
                            
                            echo '<hr><h5>📊 Datos del Sistema:</h5>';
                            echo '<div class="check-item check-success">';
                            echo "<strong>Usuarios:</strong> $totalUsuarios | ";
                            echo "<strong>Proveedores:</strong> $totalProveedores | ";
                            echo "<strong>Compras:</strong> $totalCompras";
                            echo '</div>';
                            
                        } catch (Exception $e) {
                            echo '<div class="check-item check-error">';
                            echo '<strong>✗ Conexión a Base de Datos:</strong> Error<br>';
                            echo '<small>' . htmlspecialchars($e->getMessage()) . '</small>';
                            echo '</div>';
                            $errores++;
                        }
                    }
                    
                    // 7. Verificar carpetas
                    $carpetas = ['models', 'views', 'controllers', 'assets', 'assets/css', 'assets/js'];
                    echo '<hr><h5>📁 Verificando Estructura:</h5>';
                    foreach ($carpetas as $carpeta) {
                        echo '<div class="check-item ';
                        if (is_dir(__DIR__ . '/' . $carpeta)) {
                            echo 'check-success"><strong>✓ Carpeta "$carpeta":</strong> Existe';
                        } else {
                            echo 'check-warning"><strong>⚠ Carpeta "$carpeta":</strong> No existe';
                            $advertencias++;
                        }
                        echo '</div>';
                    }
                    
                    // 8. Verificar permisos de sesión
                    echo '<hr><h5>🔐 Verificando Sesiones:</h5>';
                    echo '<div class="check-item ';
                    if (session_start()) {
                        echo 'check-success"><strong>✓ Sesiones PHP:</strong> Funcionando';
                        session_destroy();
                    } else {
                        echo 'check-error"><strong>✗ Sesiones PHP:</strong> Error';
                        $errores++;
                    }
                    echo '</div>';
                    
                    // Resumen final
                    echo '<hr>';
                    if ($errores === 0 && $advertencias === 0) {
                        echo '<div class="alert alert-success">';
                        echo '<h5>✅ ¡Todo está perfecto!</h5>';
                        echo '<p>El sistema está correctamente configurado y listo para usar.</p>';
                        echo '<a href="index.php" class="btn btn-success">Ir al Sistema</a>';
                        echo '</div>';
                    } elseif ($errores === 0) {
                        echo '<div class="alert alert-warning">';
                        echo '<h5>⚠ Sistema funcional con advertencias</h5>';
                        echo '<p>El sistema puede funcionar pero hay ' . $advertencias . ' advertencia(s).</p>';
                        echo '<a href="index.php" class="btn btn-warning">Ir al Sistema</a>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-danger">';
                        echo '<h5>❌ Errores encontrados</h5>';
                        echo '<p>Se encontraron ' . $errores . ' error(es) crítico(s). Por favor corrija los errores antes de continuar.</p>';
                        echo '<a href="INSTALACION.md" class="btn btn-primary">Ver Guía de Instalación</a>';
                        echo '</div>';
                    }
                    ?>
                    
                    <hr>
                    <div class="text-muted small">
                        <strong>Información del Servidor:</strong><br>
                        Sistema Operativo: <?php echo PHP_OS; ?><br>
                        Servidor: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'; ?><br>
                        Directorio: <?php echo __DIR__; ?><br>
                        <br>
                        <strong>⚠️ IMPORTANTE:</strong> Elimine o proteja este archivo en producción.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
