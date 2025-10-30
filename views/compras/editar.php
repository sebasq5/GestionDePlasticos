<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/CompraController.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

verificarAcceso(['admin']);

$compraController = new CompraController();
$proveedorController = new ProveedorController();

$id = $_GET['id'] ?? 0;
$compra = $compraController->obtener($id);

if (!$compra) {
    $_SESSION['mensaje'] = 'Compra no encontrada';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: /plasticos/views/compras/listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $compraController->actualizar($id, $_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/compras/listar.php');
        exit;
    }
}

$proveedores = $proveedorController->listar();

generarEncabezado("Editar Compra");
generarNavegacion('admin');
?>

<div class="container py-4">
    <h1><i class="fas fa-edit"></i> Editar Compra</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Proveedor</label>
                        <select name="id_proveedor" class="form-select" required>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?php echo $proveedor['id_proveedor']; ?>" 
                                    <?php echo $proveedor['id_proveedor'] == $compra['id_proveedor'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Compra</label>
                        <input type="date" name="fecha_compra" class="form-control" 
                               value="<?php echo $compra['fecha_compra']; ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tipo de Plástico</label>
                    <input type="text" name="tipo_plastico" class="form-control" 
                           value="<?php echo htmlspecialchars($compra['tipo_plastico']); ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cantidad (kg)</label>
                        <input type="number" name="cantidad_kg" class="form-control" step="0.01" 
                               value="<?php echo $compra['cantidad_kg']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Costo Unitario</label>
                        <input type="number" name="costo_unitario" class="form-control" step="0.01" 
                               value="<?php echo $compra['costo_unitario']; ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3"><?php echo htmlspecialchars($compra['observaciones'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Actualizar</button>
                <a href="/plasticos/views/compras/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
