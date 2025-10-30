<?php
/**
 * Modelo: Proveedor
 * 
 * Maneja todas las operaciones relacionadas con proveedores
 * Incluye funcionalidades CRUD completas
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

require_once __DIR__ . '/../config/conexion.php';

class Proveedor {
    private $db;
    
    /**
     * Constructor - Inicializa la conexión a base de datos
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Obtiene todos los proveedores
     * 
     * @return array Lista de proveedores
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT * FROM proveedores ORDER BY nombre_empresa ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener proveedores: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un proveedor por su ID
     * 
     * @param int $id ID del proveedor
     * @return array|false Datos del proveedor o false
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM proveedores WHERE id_proveedor = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener proveedor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea un nuevo proveedor
     * 
     * @param array $datos Datos del proveedor
     * @return bool True si se creó exitosamente, false si no
     */
    public function crear($datos) {
        try {
            // Validar que el RUC no exista
            if ($this->existeRuc($datos['ruc'])) {
                return false;
            }
            
            $sql = "INSERT INTO proveedores (nombre_empresa, ruc, telefono, direccion) 
                    VALUES (:nombre_empresa, :ruc, :telefono, :direccion)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'nombre_empresa' => $datos['nombre_empresa'],
                'ruc' => $datos['ruc'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion']
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear proveedor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un proveedor existente
     * 
     * @param int $id ID del proveedor
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó, false si no
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE proveedores 
                    SET nombre_empresa = :nombre_empresa, 
                        ruc = :ruc, 
                        telefono = :telefono, 
                        direccion = :direccion 
                    WHERE id_proveedor = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'nombre_empresa' => $datos['nombre_empresa'],
                'ruc' => $datos['ruc'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion']
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar proveedor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un proveedor
     * 
     * @param int $id ID del proveedor
     * @return bool True si se eliminó, false si no
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM proveedores WHERE id_proveedor = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar proveedor: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si un RUC ya está registrado
     * 
     * @param string $ruc RUC a verificar
     * @param int $excluirId ID de proveedor a excluir (para edición)
     * @return bool True si existe, false si no
     */
    public function existeRuc($ruc, $excluirId = null) {
        try {
            if ($excluirId) {
                $sql = "SELECT COUNT(*) FROM proveedores WHERE ruc = :ruc AND id_proveedor != :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['ruc' => $ruc, 'id' => $excluirId]);
            } else {
                $sql = "SELECT COUNT(*) FROM proveedores WHERE ruc = :ruc";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['ruc' => $ruc]);
            }
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar RUC: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el total de compras de un proveedor
     * 
     * @param int $id ID del proveedor
     * @return int Número de compras
     */
    public function obtenerTotalCompras($id) {
        try {
            $sql = "SELECT COUNT(*) FROM compras WHERE id_proveedor = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar compras: " . $e->getMessage());
            return 0;
        }
    }
}
?>
