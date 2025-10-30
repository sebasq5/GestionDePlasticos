<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/UsuarioController.php';

verificarAcceso(['admin']);

$controller = new UsuarioController();
$id = $_GET['id'] ?? 0;
$usuario = $controller->obtener($id);

if (!$usuario) {
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: /plasticos/views/usuarios/listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->actualizar($id, $_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/usuarios/listar.php');
        exit;
    }
}

generarEncabezado("Editar Usuario");
generarNavegacion('admin');
?>

<div class="container py-4">
    <h1><i class="fas fa-edit"></i> Editar Usuario</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" name="contraseña" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="empleado" <?php echo $usuario['rol'] === 'empleado' ? 'selected' : ''; ?>>Empleado</option>
                        <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="proveedor" <?php echo $usuario['rol'] === 'proveedor' ? 'selected' : ''; ?>>Proveedor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="activo" <?php echo $usuario['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $usuario['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                <a href="/plasticos/views/usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
