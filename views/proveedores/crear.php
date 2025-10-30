<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

verificarAcceso(['admin']);

$controller = new ProveedorController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->crear($_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/proveedores/listar.php');
        exit;
    }
}

generarEncabezado("Crear Proveedor");
generarNavegacion('admin');
?>

<div class="container py-4">
    <h1><i class="fas fa-plus-circle"></i> Registrar Nuevo Proveedor</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Empresa</label>
                    <input type="text" name="nombre_empresa" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">RUC (13 dígitos)</label>
                    <input type="text" name="ruc" class="form-control" maxlength="13" pattern="[0-9]{13}" required>
                    <small class="text-muted">Ingrese 13 dígitos numéricos</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                <a href="/plasticos/views/proveedores/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
