<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/CompraController.php';

verificarAcceso(['admin', 'empleado', 'proveedor']);

$controller = new CompraController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $resultado = $controller->eliminar($_POST['id']);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    header('Location: /plasticos/views/compras/listar.php');
    exit;
}

// Filtrar por proveedor si es rol proveedor
$idProveedorFiltro = null;
if ($_SESSION['usuario_rol'] === 'proveedor') {
    // En producción, aquí se obtendría el ID del proveedor asociado al usuario
    $idProveedorFiltro = 1; // Ejemplo
}

$compras = $controller->listar($idProveedorFiltro);

generarEncabezado("Gestión de Compras");
generarNavegacion($_SESSION['usuario_rol']);
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-shopping-cart"></i> Registro de Compras</h1>
        </div>
        <?php if ($_SESSION['usuario_rol'] !== 'proveedor'): ?>
        <div class="col-md-4 text-end">
            <a href="/plasticos/views/compras/crear.php" class="btn btn-warning">
                <i class="fas fa-cart-plus"></i> Nueva Compra
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php
    if (isset($_SESSION['mensaje'])) {
        mostrarAlerta($_SESSION['mensaje'], $_SESSION['tipo_mensaje'] ?? 'info');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
    }
    ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-warning">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Tipo Plástico</th>
                            <th>Cantidad (kg)</th>
                            <th>Costo Unit.</th>
                            <th>Total</th>
                            <th>Usuario</th>
                            <?php if ($_SESSION['usuario_rol'] !== 'proveedor'): ?>
                            <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td><?php echo $compra['id_compra']; ?></td>
                            <td><?php echo formatearFecha($compra['fecha_compra']); ?></td>
                            <td><?php echo htmlspecialchars($compra['nombre_proveedor']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($compra['tipo_plastico']); ?></span></td>
                            <td><?php echo number_format($compra['cantidad_kg'], 2); ?></td>
                            <td><?php echo formatearMoneda($compra['costo_unitario']); ?></td>
                            <td><strong><?php echo formatearMoneda($compra['total']); ?></strong></td>
                            <td><?php echo htmlspecialchars($compra['nombre_usuario']); ?></td>
                            <?php if ($_SESSION['usuario_rol'] !== 'proveedor'): ?>
                            <td>
                                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                <a href="/plasticos/views/compras/editar.php?id=<?php echo $compra['id_compra']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar compra?');">
                                    <input type="hidden" name="id" value="<?php echo $compra['id_compra']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
