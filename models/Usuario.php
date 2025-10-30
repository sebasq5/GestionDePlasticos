<?php
/**
 * Modelo: Usuario
 * 
 * Maneja todas las operaciones relacionadas con usuarios
 * Incluye funcionalidades CRUD y autenticación
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $db;
    
    /**
     * Constructor - Inicializa la conexión a base de datos
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Autentica un usuario con correo y contraseña
     * 
     * @param string $correo Correo electrónico del usuario
     * @param string $contraseña Contraseña sin encriptar
     * @return array|false Datos del usuario si es válido, false si no
     */
    public function autenticar($correo, $contraseña) {
        try {
            $sql = "SELECT * FROM usuarios WHERE correo = :correo AND estado = 'activo' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['correo' => $correo]);
            
            $usuario = $stmt->fetch();
            
            // Verificar si existe el usuario y la contraseña es correcta
            if ($usuario && password_verify($contraseña, $usuario['contraseña_hash'])) {
                // No retornar el hash de contraseña por seguridad
                unset($usuario['contraseña_hash']);
                return $usuario;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todos los usuarios
     * 
     * @return array Lista de usuarios
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT id_usuario, nombre, correo, rol, estado, fecha_registro 
                    FROM usuarios ORDER BY fecha_registro DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id_usuario, nombre, correo, rol, estado, fecha_registro 
                    FROM usuarios WHERE id_usuario = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea un nuevo usuario
     * 
     * @param array $datos Datos del usuario (nombre, correo, contraseña, rol, estado)
     * @return bool True si se creó exitosamente, false si no
     */
    public function crear($datos) {
        try {
            // Validar que el correo no exista
            if ($this->existeCorreo($datos['correo'])) {
                return false;
            }
            
            $sql = "INSERT INTO usuarios (nombre, correo, contraseña_hash, rol, estado) 
                    VALUES (:nombre, :correo, :contraseña_hash, :rol, :estado)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'nombre' => $datos['nombre'],
                'correo' => $datos['correo'],
                'contraseña_hash' => password_hash($datos['contraseña'], PASSWORD_DEFAULT),
                'rol' => $datos['rol'],
                'estado' => $datos['estado']
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un usuario existente
     * 
     * @param int $id ID del usuario
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó, false si no
     */
    public function actualizar($id, $datos) {
        try {
            // Si viene contraseña, actualizarla también
            if (!empty($datos['contraseña'])) {
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, correo = :correo, 
                            contraseña_hash = :contraseña_hash, rol = :rol, estado = :estado 
                        WHERE id_usuario = :id";
                
                $params = [
                    'id' => $id,
                    'nombre' => $datos['nombre'],
                    'correo' => $datos['correo'],
                    'contraseña_hash' => password_hash($datos['contraseña'], PASSWORD_DEFAULT),
                    'rol' => $datos['rol'],
                    'estado' => $datos['estado']
                ];
            } else {
                // Actualizar sin cambiar la contraseña
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, correo = :correo, rol = :rol, estado = :estado 
                        WHERE id_usuario = :id";
                
                $params = [
                    'id' => $id,
                    'nombre' => $datos['nombre'],
                    'correo' => $datos['correo'],
                    'rol' => $datos['rol'],
                    'estado' => $datos['estado']
                ];
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un usuario
     * 
     * @param int $id ID del usuario
     * @return bool True si se eliminó, false si no
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si un correo ya está registrado
     * 
     * @param string $correo Correo a verificar
     * @param int $excluirId ID de usuario a excluir de la búsqueda (para edición)
     * @return bool True si existe, false si no
     */
    public function existeCorreo($correo, $excluirId = null) {
        try {
            if ($excluirId) {
                $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id_usuario != :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['correo' => $correo, 'id' => $excluirId]);
            } else {
                $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['correo' => $correo]);
            }
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar correo: " . $e->getMessage());
            return false;
        }
    }
}
?>
