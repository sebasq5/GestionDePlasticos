<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/ProveedorController.php';

verificarAcceso(['admin', 'empleado']);

$controller = new ProveedorController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $resultado = $controller->eliminar($_POST['id']);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    header('Location: /plasticos/views/proveedores/listar.php');
    exit;
}

$proveedores = $controller->listar();

generarEncabezado("Gestión de Proveedores");
generarNavegacion($_SESSION['usuario_rol']);
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-building"></i> Proveedores</h1>
        </div>
        <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
        <div class="col-md-4 text-end">
            <a href="/plasticos/views/proveedores/crear.php" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nuevo Proveedor
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
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>RUC</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                            <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?php echo $proveedor['id_proveedor']; ?></td>
                            <td><strong><?php echo htmlspecialchars($proveedor['nombre_empresa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($proveedor['ruc']); ?></td>
                            <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($proveedor['direccion']); ?></td>
                            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                            <td>
                                <a href="/plasticos/views/proveedores/editar.php?id=<?php echo $proveedor['id_proveedor']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar proveedor?');">
                                    <input type="hidden" name="id" value="<?php echo $proveedor['id_proveedor']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
