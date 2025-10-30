<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/CompraController.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

verificarAcceso(['admin', 'empleado']);

$compraController = new CompraController();
$proveedorController = new ProveedorController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['id_usuario'] = $_SESSION['usuario_id'];
    $resultado = $compraController->crear($_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/compras/listar.php');
        exit;
    }
}

$proveedores = $proveedorController->listar();

generarEncabezado("Registrar Compra");
generarNavegacion($_SESSION['usuario_rol']);
?>

<div class="container py-4">
    <h1><i class="fas fa-cart-plus"></i> Registrar Nueva Compra</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Proveedor</label>
                        <select name="id_proveedor" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?php echo $proveedor['id_proveedor']; ?>">
                                <?php echo htmlspecialchars($proveedor['nombre_empresa']); ?> - RUC: <?php echo $proveedor['ruc']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Compra</label>
                        <input type="date" name="fecha_compra" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Tipo de Plástico</label>
                        <input type="text" name="tipo_plastico" class="form-control" 
                               placeholder="Ej: PET, HDPE, PVC, LDPE, PP, PS" required>
                        <small class="text-muted">Tipos comunes: PET, HDPE, PVC, LDPE, PP, PS</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cantidad (kg)</label>
                        <input type="number" name="cantidad_kg" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Costo Unitario (por kg)</label>
                        <input type="number" name="costo_unitario" class="form-control" step="0.01" min="0.01" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Observaciones (opcional)</label>
                    <textarea name="observaciones" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Registrar Compra</button>
                <a href="/plasticos/views/compras/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
