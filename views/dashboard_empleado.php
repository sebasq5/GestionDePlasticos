<?php
/**
 * Dashboard del Empleado
 * Panel de control para empleados con acceso limitado
 */

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/../controllers/ProveedorController.php';
require_once __DIR__ . '/../controllers/CompraController.php';

// Verificar acceso solo para empleado
verificarAcceso(['empleado']);

// Instanciar controladores
$proveedorController = new ProveedorController();
$compraController = new CompraController();

// Obtener datos
$proveedores = $proveedorController->listar();
$compras = $compraController->listar();
$estadisticas = $compraController->obtenerEstadisticas();

generarEncabezado("Dashboard - Empleado");
generarNavegacion('empleado');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-6">
                <i class="fas fa-briefcase"></i> Panel de Empleado
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
        <div class="col-md-4">
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
                        Ver proveedores <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Compras Totales</h5>
                            <h2 class="mb-0"><?php echo $estadisticas['total_compras']; ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/plasticos/views/compras/listar.php" class="text-white text-decoration-none">
                        Ver compras <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Kilogramos</h5>
                            <h2 class="mb-0"><?php echo number_format($estadisticas['kg_totales'], 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-weight fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-white">Total acumulado</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Accesos rápidos -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Acciones Disponibles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="/plasticos/views/compras/crear.php" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Registrar Nueva Compra
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="/plasticos/views/compras/listar.php" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-list"></i> Ver Todas las Compras
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lista de proveedores -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Proveedores Registrados</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($proveedores)): ?>
                        <p class="text-center text-muted">No hay proveedores registrados</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>RUC</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($proveedores as $proveedor): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($proveedor['ruc']); ?></td>
                                        <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($proveedor['direccion']); ?></td>
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
