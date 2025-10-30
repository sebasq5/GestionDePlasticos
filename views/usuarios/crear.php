<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/UsuarioController.php';

verificarAcceso(['admin']);

$controller = new UsuarioController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->crear($_POST);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    if ($resultado['exito']) {
        header('Location: /plasticos/views/usuarios/listar.php');
        exit;
    }
}

generarEncabezado("Crear Usuario");
generarNavegacion('admin');
?>

<div class="container py-4">
    <h1><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h1>
    
    <?php
    if (isset($_SESSION['mensaje'])) {
        mostrarAlerta($_SESSION['mensaje'], $_SESSION['tipo_mensaje'] ?? 'info');
        if (isset($resultado['errores'])) {
            foreach ($resultado['errores'] as $error) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
            }
        }
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
    }
    ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="contraseña" class="form-control" required minlength="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Administrador</option>
                        <option value="proveedor">Proveedor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                <a href="/plasticos/views/usuarios/listar.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
