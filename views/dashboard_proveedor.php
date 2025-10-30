<?php
/**
 * Dashboard del Proveedor
 * Panel de control para proveedores (vista solo lectura)
 */

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/../controllers/CompraController.php';
require_once __DIR__ . '/../controllers/ProveedorController.php';

// Verificar acceso solo para proveedor
verificarAcceso(['proveedor']);

// Instanciar controladores
$compraController = new CompraController();
$proveedorController = new ProveedorController();

// Buscar el proveedor asociado al correo del usuario
$correoUsuario = $_SESSION['usuario_correo'];
// Aquí asumimos que el correo del usuario proveedor está vinculado
// En un sistema real, habría una tabla intermedia o un campo en proveedores
$proveedores = $proveedorController->listar();
$miProveedor = null;

// Obtener todas las compras para luego filtrar
$todasCompras = $compraController->listar();

generarEncabezado("Dashboard - Proveedor");
generarNavegacion('proveedor');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-6">
                <i class="fas fa-building"></i> Panel de Proveedor
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
    
    <!-- Información del proveedor -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de Mi Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Nota:</strong> Los proveedores tienen acceso de solo lectura.
                        Pueden ver su información y las compras realizadas a su empresa.
                    </div>
                    
                    <?php if (count($proveedores) > 0): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($proveedores[0]['nombre_empresa']); ?></p>
                                <p><strong>RUC:</strong> <?php echo htmlspecialchars($proveedores[0]['ruc']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($proveedores[0]['telefono']); ?></p>
                                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($proveedores[0]['direccion']); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No se encontró información del proveedor</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas de compras -->
    <?php
    $misCompras = [];
    $totalMisCompras = 0;
    $totalKg = 0;
    
    if (count($proveedores) > 0) {
        $idProveedor = $proveedores[0]['id_proveedor'];
        $misCompras = $compraController->listar($idProveedor);
        
        foreach ($misCompras as $compra) {
            $totalMisCompras += $compra['total'];
            $totalKg += $compra['cantidad_kg'];
        }
    }
    ?>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Compras</h5>
                            <h2 class="mb-0"><?php echo count($misCompras); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Kilogramos</h5>
                            <h2 class="mb-0"><?php echo number_format($totalKg, 0); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-weight fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Monto Total</h5>
                            <h2 class="mb-0"><?php echo formatearMoneda($totalMisCompras); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Historial de compras -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Historial de Compras a Mi Empresa</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($misCompras)): ?>
                        <p class="text-center text-muted">No hay compras registradas a su empresa</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Tipo Plástico</th>
                                        <th>Cantidad (kg)</th>
                                        <th>Costo Unitario</th>
                                        <th>Total</th>
                                        <th>Registrado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($misCompras as $compra): ?>
                                    <tr>
                                        <td><?php echo $compra['id_compra']; ?></td>
                                        <td><?php echo formatearFecha($compra['fecha_compra']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($compra['tipo_plastico']); ?></span></td>
                                        <td><?php echo number_format($compra['cantidad_kg'], 2); ?></td>
                                        <td><?php echo formatearMoneda($compra['costo_unitario']); ?></td>
                                        <td><strong><?php echo formatearMoneda($compra['total']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($compra['nombre_usuario']); ?></td>
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
