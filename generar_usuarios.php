<?php
/**
 * Script para generar usuarios con contraseñas correctas
 * Este script limpia los usuarios existentes y crea nuevos con hash correcto
 */

// Configuración de conexión directa
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'plastico_db');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Generar Usuarios - Sistema Plásticos</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <div class='card shadow'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'><i class='bi bi-key'></i> Generador de Usuarios</h3>
                </div>
                <div class='card-body'>";

try {
    // Crear conexión PDO directa
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Contraseña por defecto
    $contraseña = '12345';
    $hash = password_hash($contraseña, PASSWORD_BCRYPT);
    
    echo "<div class='alert alert-info'>
            <strong>Contraseña para todos los usuarios:</strong> <code>12345</code>
          </div>";
    
    echo "<div class='alert alert-secondary'>
            <strong>Hash generado:</strong><br>
            <code style='word-break: break-all;'>{$hash}</code>
          </div>";
    
    // Eliminar compras primero (por la restricción de foreign key)
    echo "<h5 class='mt-4'>1. Limpiando compras existentes...</h5>";
    $db->exec("DELETE FROM compras");
    echo "<div class='alert alert-success'>✓ Compras eliminadas</div>";
    
    // Eliminar usuarios
    echo "<h5 class='mt-4'>2. Limpiando usuarios existentes...</h5>";
    $db->exec("DELETE FROM usuarios");
    echo "<div class='alert alert-success'>✓ Usuarios eliminados</div>";
    
    // Crear nuevos usuarios
    echo "<h5 class='mt-4'>3. Creando nuevos usuarios...</h5>";
    
    $usuarios = [
        [
            'nombre' => 'Administrador del Sistema',
            'correo' => 'admin@plasticos.com',
            'rol' => 'admin',
            'estado' => 'activo'
        ],
        [
            'nombre' => 'Juan Pérez',
            'correo' => 'empleado@plasticos.com',
            'rol' => 'empleado',
            'estado' => 'activo'
        ],
        [
            'nombre' => 'María González',
            'correo' => 'maria@proveedor.com',
            'rol' => 'proveedor',
            'estado' => 'activo'
        ]
    ];
    
    $stmt = $db->prepare("
        INSERT INTO usuarios (nombre, correo, contraseña_hash, rol, estado) 
        VALUES (:nombre, :correo, :password, :rol, :estado)
    ");
    
    echo "<div class='table-responsive'>
            <table class='table table-bordered table-striped'>
                <thead class='table-dark'>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Resultado</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($usuarios as $usuario) {
        $stmt->execute([
            ':nombre' => $usuario['nombre'],
            ':correo' => $usuario['correo'],
            ':password' => $hash,
            ':rol' => $usuario['rol'],
            ':estado' => $usuario['estado']
        ]);
        
        echo "<tr>
                <td>{$usuario['nombre']}</td>
                <td><strong>{$usuario['correo']}</strong></td>
                <td><span class='badge bg-" . ($usuario['rol'] === 'admin' ? 'danger' : ($usuario['rol'] === 'empleado' ? 'primary' : 'success')) . "'>{$usuario['rol']}</span></td>
                <td><span class='badge bg-success'>{$usuario['estado']}</span></td>
                <td><span class='text-success'>✓ Creado</span></td>
              </tr>";
    }
    
    echo "</tbody></table></div>";
    
    // Verificar creación
    echo "<h5 class='mt-4'>4. Verificando usuarios creados...</h5>";
    $result = $db->query("SELECT id_usuario, nombre, correo, rol, estado FROM usuarios ORDER BY id_usuario");
    $total = $result->rowCount();
    
    echo "<div class='alert alert-success'>
            <strong>✓ Total de usuarios en la base de datos: {$total}</strong>
          </div>";
    
    // Recrear compras de prueba
    echo "<h5 class='mt-4'>5. Recreando compras de prueba...</h5>";
    
    // Obtener el ID del empleado recién creado
    $empleado = $db->query("SELECT id_usuario FROM usuarios WHERE rol = 'empleado' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $idEmpleado = $empleado['id_usuario'];
    
    $comprasSql = "
        INSERT INTO compras (id_usuario, id_proveedor, fecha_compra, tipo_plastico, cantidad_kg, costo_unitario, observaciones) VALUES
        ({$idEmpleado}, 1, '2025-10-15', 'PET', 150.50, 0.80, 'Primera compra del mes'),
        ({$idEmpleado}, 2, '2025-10-20', 'HDPE', 200.00, 0.75, 'Material de alta calidad'),
        ({$idEmpleado}, 1, '2025-10-25', 'PVC', 100.25, 0.90, 'Entrega inmediata')
    ";
    
    $db->exec($comprasSql);
    echo "<div class='alert alert-success'>✓ 3 compras de prueba creadas</div>";
    
    // Prueba de login
    echo "<h5 class='mt-4'>6. Prueba de verificación de contraseña...</h5>";
    
    $testUser = $db->query("SELECT contraseña_hash FROM usuarios WHERE correo = 'admin@plasticos.com'")->fetch(PDO::FETCH_ASSOC);
    
    if ($testUser && password_verify($contraseña, $testUser['contraseña_hash'])) {
        echo "<div class='alert alert-success'>
                <strong>✓ La contraseña '12345' funciona correctamente</strong>
              </div>";
    } else {
        echo "<div class='alert alert-danger'>
                <strong>✗ Error: La contraseña no funciona</strong>
              </div>";
    }
    
    echo "<hr class='my-4'>
          <div class='alert alert-info'>
            <h5><i class='bi bi-info-circle'></i> Credenciales de Acceso:</h5>
            <table class='table table-sm mt-3'>
                <tbody>
                    <tr>
                        <td><strong>Admin:</strong></td>
                        <td>admin@plasticos.com</td>
                        <td>/</td>
                        <td>12345</td>
                    </tr>
                    <tr>
                        <td><strong>Empleado:</strong></td>
                        <td>empleado@plasticos.com</td>
                        <td>/</td>
                        <td>12345</td>
                    </tr>
                    <tr>
                        <td><strong>Proveedor:</strong></td>
                        <td>maria@proveedor.com</td>
                        <td>/</td>
                        <td>12345</td>
                    </tr>
                </tbody>
            </table>
          </div>";
    
    echo "<div class='d-grid gap-2 mt-4'>
            <a href='/plasticos/' class='btn btn-primary btn-lg'>
                <i class='bi bi-box-arrow-in-right'></i> Ir al Login
            </a>
            <a href='/plasticos/generar_usuarios.php' class='btn btn-secondary'>
                <i class='bi bi-arrow-clockwise'></i> Regenerar Usuarios
            </a>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            <strong>Error:</strong> {$e->getMessage()}
          </div>";
}

echo "    </div>
            </div>
        </div>
    </div>
</div>

<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'>
</body>
</html>";
?>
