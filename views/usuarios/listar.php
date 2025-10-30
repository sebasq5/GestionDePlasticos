<?php
/**
 * Listar Usuarios
 * Vista para administradores
 */

require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../../controllers/UsuarioController.php';

verificarAcceso(['admin']);

$controller = new UsuarioController();

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $resultado = $controller->eliminar($_POST['id']);
    $_SESSION['mensaje'] = $resultado['mensaje'];
    $_SESSION['tipo_mensaje'] = $resultado['exito'] ? 'success' : 'danger';
    header('Location: /plasticos/views/usuarios/listar.php');
    exit;
}

$usuarios = $controller->listar();

generarEncabezado("Gestión de Usuarios");
generarNavegacion('admin');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="/plasticos/views/usuarios/crear.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Usuario
            </a>
        </div>
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
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $usuario['rol'] === 'admin' ? 'danger' : ($usuario['rol'] === 'empleado' ? 'primary' : 'success'); ?>">
                                    <?php echo ucfirst($usuario['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $usuario['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($usuario['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo formatearFecha($usuario['fecha_registro'], 'd/m/Y H:i'); ?></td>
                            <td>
                                <a href="/plasticos/views/usuarios/editar.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($usuario['id_usuario'] != $_SESSION['usuario_id']): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                    <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php generarPiePagina(); ?>
