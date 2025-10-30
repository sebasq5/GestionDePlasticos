<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestión de Plásticos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-recycle fa-3x mb-3"></i>
                        <h3>Sistema de Gestión de Plásticos</h3>
                        <p class="mb-0">Inicie sesión para continuar</p>
                    </div>
                    
                    <div class="login-body">
                        <?php
                        session_start();
                        
                        // Si ya hay sesión activa, redirigir al dashboard
                        if (isset($_SESSION['usuario_id'])) {
                            header('Location: index.php');
                            exit;
                        }
                        
                        // Mostrar mensajes de error o éxito
                        if (isset($_SESSION['mensaje'])) {
                            $tipo = $_SESSION['tipo_mensaje'] ?? 'danger';
                            echo '<div class="alert alert-' . $tipo . ' alert-dismissible fade show" role="alert">';
                            echo '<i class="fas fa-' . ($tipo === 'success' ? 'check-circle' : 'exclamation-triangle') . '"></i> ';
                            echo htmlspecialchars($_SESSION['mensaje']);
                            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                            echo '</div>';
                            unset($_SESSION['mensaje']);
                            unset($_SESSION['tipo_mensaje']);
                        }
                        ?>
                        
                                <form action="/plasticos/index.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="correo" class="form-label">
                                    <i class="fas fa-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" class="form-control form-control-lg" 
                                       id="correo" name="correo" required
                                       placeholder="ejemplo@correo.com"
                                       autocomplete="email">
                            </div>
                            
                            <div class="mb-4">
                                <label for="contraseña" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" class="form-control form-control-lg" 
                                       id="contraseña" name="contraseña" required
                                       placeholder="Ingrese su contraseña"
                                       autocomplete="current-password">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login btn-lg w-100">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        
                    </div>
                </div>
                
                <div class="text-center mt-3 text-white">
                    <small>
                        <i class="fas fa-recycle"></i> Sistema de Gestión de Plásticos | Desarrollado por Sebastian Sanmartin - Aivra
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
