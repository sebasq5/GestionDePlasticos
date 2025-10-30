<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

verificarAcceso(['admin']);

$controller = new ProveedorController();
$id = $_GET['id'] ?? 0;
$proveedor = $controller->obtener($id);

if (!$proveedor) {
    $_SESSION['mensaje'] = 'Proveedor no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: /plasticos/views/proveedores/listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->actualizar($id, $_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/proveedores/listar.php');
        exit;
    }
}

generarEncabezado("Editar Proveedor");
generarNavegacion('admin');
?>

<div class="container py-4">
    <h1><i class="fas fa-edit"></i> Editar Proveedor</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" class="form-control" value="<?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" class="form-control" value="<?php echo htmlspecialchars($proveedor['ruc']); ?>" maxlength="13" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3" required><?php echo htmlspecialchars($proveedor['direccion']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar</button>
                <a href="/plasticos/views/proveedores/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
