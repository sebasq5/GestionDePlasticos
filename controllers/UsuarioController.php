<?php
/**
 * Controlador: Usuario
 * 
 * Maneja las peticiones y lógica de negocio para usuarios
 * Valida datos y coordina entre vistas y modelo
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

session_start();
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $modelo;
    
    /**
     * Constructor - Inicializa el modelo
     */
    public function __construct() {
        $this->modelo = new Usuario();
    }
    
    /**
     * Procesa el login de usuario
     * 
     * @param string $correo Correo electrónico
     * @param string $contraseña Contraseña
     * @return array Resultado de la operación
     */
    public function login($correo, $contraseña) {
        // Validar datos de entrada
        $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return [
                'exito' => false,
                'mensaje' => 'El correo electrónico no es válido'
            ];
        }
        
        if (empty($contraseña)) {
            return [
                'exito' => false,
                'mensaje' => 'La contraseña es requerida'
            ];
        }
        
        // Autenticar usuario
        $usuario = $this->modelo->autenticar($correo, $contraseña);
        
        if ($usuario) {
            // Guardar datos en sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            $_SESSION['ultimo_acceso'] = time();
            
            return [
                'exito' => true,
                'mensaje' => 'Login exitoso',
                'rol' => $usuario['rol']
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Credenciales incorrectas o usuario inactivo'
            ];
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Verifica si hay una sesión activa
     * 
     * @return bool True si hay sesión activa
     */
    public function verificarSesion() {
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Verifica si el usuario tiene un rol específico
     * 
     * @param string|array $roles Rol o roles permitidos
     * @return bool True si tiene el rol
     */
    public function verificarRol($roles) {
        if (!$this->verificarSesion()) {
            return false;
        }
        
        if (is_array($roles)) {
            return in_array($_SESSION['usuario_rol'], $roles);
        }
        
        return $_SESSION['usuario_rol'] === $roles;
    }
    
    /**
     * Lista todos los usuarios
     * 
     * @return array Lista de usuarios
     */
    public function listar() {
        return $this->modelo->obtenerTodos();
    }
    
    /**
     * Obtiene un usuario por ID
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario
     */
    public function obtener($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return false;
        }
        return $this->modelo->obtenerPorId($id);
    }
    
    /**
     * Crea un nuevo usuario
     * 
     * @param array $datos Datos del usuario
     * @return array Resultado de la operación
     */
    public function crear($datos) {
        // Validar datos
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            return [
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $errores
            ];
        }
        
        // Sanitizar datos
        $datos['nombre'] = filter_var($datos['nombre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['correo'] = filter_var($datos['correo'], FILTER_SANITIZE_EMAIL);
        
        // Intentar crear
        if ($this->modelo->crear($datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Usuario creado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al crear usuario. El correo puede estar duplicado.'
            ];
        }
    }
    
    /**
     * Actualiza un usuario existente
     * 
     * @param int $id ID del usuario
     * @param array $datos Datos a actualizar
     * @return array Resultado de la operación
     */
    public function actualizar($id, $datos) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de usuario inválido'
            ];
        }
        
        // Validar datos
        $errores = $this->validarDatos($datos, $id);
        
        if (!empty($errores)) {
            return [
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $errores
            ];
        }
        
        // Sanitizar datos
        $datos['nombre'] = filter_var($datos['nombre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['correo'] = filter_var($datos['correo'], FILTER_SANITIZE_EMAIL);
        
        // Intentar actualizar
        if ($this->modelo->actualizar($id, $datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Usuario actualizado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar usuario'
            ];
        }
    }
    
    /**
     * Elimina un usuario
     * 
     * @param int $id ID del usuario
     * @return array Resultado de la operación
     */
    public function eliminar($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de usuario inválido'
            ];
        }
        
        // No permitir eliminar el usuario actual
        if ($id == $_SESSION['usuario_id']) {
            return [
                'exito' => false,
                'mensaje' => 'No puede eliminar su propio usuario'
            ];
        }
        
        if ($this->modelo->eliminar($id)) {
            return [
                'exito' => true,
                'mensaje' => 'Usuario eliminado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al eliminar usuario'
            ];
        }
    }
    
    /**
     * Valida los datos de un usuario
     * 
     * @param array $datos Datos a validar
     * @param int|null $idExcluir ID a excluir en validación de correo
     * @return array Errores encontrados
     */
    private function validarDatos($datos, $idExcluir = null) {
        $errores = [];
        
        // Validar nombre
        if (empty($datos['nombre']) || strlen($datos['nombre']) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        }
        
        // Validar correo
        if (empty($datos['correo'])) {
            $errores[] = 'El correo es requerido';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido';
        } elseif ($this->modelo->existeCorreo($datos['correo'], $idExcluir)) {
            $errores[] = 'El correo ya está registrado';
        }
        
        // Validar contraseña (solo si se proporciona)
        if (!$idExcluir && empty($datos['contraseña'])) {
            $errores[] = 'La contraseña es requerida';
        } elseif (!empty($datos['contraseña']) && strlen($datos['contraseña']) < 5) {
            $errores[] = 'La contraseña debe tener al menos 5 caracteres';
        }
        
        // Validar rol
        $rolesValidos = ['admin', 'empleado', 'proveedor'];
        if (empty($datos['rol']) || !in_array($datos['rol'], $rolesValidos)) {
            $errores[] = 'El rol no es válido';
        }
        
        // Validar estado
        $estadosValidos = ['activo', 'inactivo'];
        if (empty($datos['estado']) || !in_array($datos['estado'], $estadosValidos)) {
            $errores[] = 'El estado no es válido';
        }
        
        return $errores;
    }
}
?>
