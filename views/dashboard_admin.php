<?php
/**
 * Dashboard del Administrador
 * Panel de control con estadísticas y accesos rápidos
 */

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/ProveedorController.php';
require_once __DIR__ . '/../controllers/CompraController.php';

// Verificar acceso solo para admin
verificarAcceso(['admin']);

// Instanciar controladores
$usuarioController = new UsuarioController();
$proveedorController = new ProveedorController();
$compraController = new CompraController();

// Obtener estadísticas
$usuarios = $usuarioController->listar();
$proveedores = $proveedorController->listar();
$compras = $compraController->listar();
$estadisticas = $compraController->obtenerEstadisticas();

generarEncabezado("Dashboard - Administrador");
generarNavegacion('admin');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-6">
                <i class="fas fa-chart-line"></i> Panel de Administración
            </h1>
            <p class="text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
        </div>
    </div>
    
    <?php
    // Mostrar mensajes de sesión
    if (isset($_SESSION['mensaje'])) {
        mostrarAlerta($_SESSION['mensaje'], $_SESSION['tipo_mensaje'] ?? 'info');
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
    }
    ?>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Usuarios</h5>
                            <h2 class="mb-0"><?php echo count($usuarios); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/plasticos/views/usuarios/listar.php" class="text-white text-decoration-none">
                        Ver todos <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Proveedores</h5>
                            <h2 class="mb-0"><?php echo count($proveedores); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-building fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/plasticos/views/proveedores/listar.php" class="text-white text-decoration-none">
                        Ver todos <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Compras</h5>
                            <h2 class="mb-0"><?php echo $estadisticas['total_compras']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/plasticos/views/compras/listar.php" class="text-white text-decoration-none">
                        Ver todas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Invertido</h5>
                            <h2 class="mb-0"><?php echo formatearMoneda($estadisticas['monto_total']); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-white">Kilogramos: <?php echo number_format($estadisticas['kg_totales'], 2); ?> kg</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Accesos rápidos -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Accesos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="/plasticos/views/usuarios/crear.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-user-plus"></i> Crear Usuario
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/plasticos/views/proveedores/crear.php" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-plus-circle"></i> Registrar Proveedor
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/plasticos/views/compras/crear.php" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Nueva Compra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimas compras -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Últimas Compras Registradas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($compras)): ?>
                        <p class="text-center text-muted">No hay compras registradas</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Proveedor</th>
                                        <th>Tipo Plástico</th>
                                        <th>Cantidad (kg)</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $ultimasCompras = array_slice($compras, 0, 5);
                                    foreach ($ultimasCompras as $compra): 
                                    ?>
                                    <tr>
                                        <td><?php echo formatearFecha($compra['fecha_compra']); ?></td>
                                        <td><?php echo htmlspecialchars($compra['nombre_proveedor']); ?></td>
                                        <td><?php echo htmlspecialchars($compra['tipo_plastico']); ?></td>
                                        <td><?php echo number_format($compra['cantidad_kg'], 2); ?></td>
                                        <td><strong><?php echo formatearMoneda($compra['total']); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
