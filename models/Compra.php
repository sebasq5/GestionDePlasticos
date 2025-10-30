<?php
/**
 * Modelo: Compra
 * 
 * Maneja todas las operaciones relacionadas con compras de plástico
 * Incluye funcionalidades CRUD y reportes
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

require_once __DIR__ . '/../config/conexion.php';

class Compra {
    private $db;
    
    /**
     * Constructor - Inicializa la conexión a base de datos
     */
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Obtiene todas las compras con información relacionada
     * 
     * @param int|null $idProveedor Filtrar por proveedor (opcional)
     * @return array Lista de compras
     */
    public function obtenerTodos($idProveedor = null) {
        try {
            $sql = "SELECT c.*, 
                           u.nombre as nombre_usuario,
                           p.nombre_empresa as nombre_proveedor
                    FROM compras c
                    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                    INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor";
            
            if ($idProveedor) {
                $sql .= " WHERE c.id_proveedor = :id_proveedor";
            }
            
            $sql .= " ORDER BY c.fecha_compra DESC, c.id_compra DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($idProveedor) {
                $stmt->execute(['id_proveedor' => $idProveedor]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener compras: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene una compra por su ID
     * 
     * @param int $id ID de la compra
     * @return array|false Datos de la compra o false
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT c.*, 
                           u.nombre as nombre_usuario,
                           p.nombre_empresa as nombre_proveedor,
                           p.ruc
                    FROM compras c
                    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                    INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                    WHERE c.id_compra = :id 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener compra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crea una nueva compra
     * 
     * @param array $datos Datos de la compra
     * @return bool True si se creó exitosamente, false si no
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO compras 
                    (id_usuario, id_proveedor, fecha_compra, tipo_plastico, 
                     cantidad_kg, costo_unitario, observaciones) 
                    VALUES 
                    (:id_usuario, :id_proveedor, :fecha_compra, :tipo_plastico, 
                     :cantidad_kg, :costo_unitario, :observaciones)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id_usuario' => $datos['id_usuario'],
                'id_proveedor' => $datos['id_proveedor'],
                'fecha_compra' => $datos['fecha_compra'],
                'tipo_plastico' => $datos['tipo_plastico'],
                'cantidad_kg' => $datos['cantidad_kg'],
                'costo_unitario' => $datos['costo_unitario'],
                'observaciones' => $datos['observaciones'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear compra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza una compra existente
     * 
     * @param int $id ID de la compra
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó, false si no
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE compras 
                    SET id_proveedor = :id_proveedor,
                        fecha_compra = :fecha_compra,
                        tipo_plastico = :tipo_plastico,
                        cantidad_kg = :cantidad_kg,
                        costo_unitario = :costo_unitario,
                        observaciones = :observaciones
                    WHERE id_compra = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'id_proveedor' => $datos['id_proveedor'],
                'fecha_compra' => $datos['fecha_compra'],
                'tipo_plastico' => $datos['tipo_plastico'],
                'cantidad_kg' => $datos['cantidad_kg'],
                'costo_unitario' => $datos['costo_unitario'],
                'observaciones' => $datos['observaciones'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar compra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una compra
     * 
     * @param int $id ID de la compra
     * @return bool True si se eliminó, false si no
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM compras WHERE id_compra = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar compra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de compras
     * 
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_compras,
                        SUM(total) as monto_total,
                        SUM(cantidad_kg) as kg_totales,
                        AVG(costo_unitario) as costo_promedio
                    FROM compras";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_compras' => 0,
                'monto_total' => 0,
                'kg_totales' => 0,
                'costo_promedio' => 0
            ];
        }
    }
    
    /**
     * Obtiene compras por rango de fechas
     * 
     * @param string $fechaInicio Fecha inicial
     * @param string $fechaFin Fecha final
     * @return array Lista de compras en el rango
     */
    public function obtenerPorRangoFechas($fechaInicio, $fechaFin) {
        try {
            $sql = "SELECT c.*, 
                           u.nombre as nombre_usuario,
                           p.nombre_empresa as nombre_proveedor
                    FROM compras c
                    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                    INNER JOIN proveedores p ON c.id_proveedor = p.id_proveedor
                    WHERE c.fecha_compra BETWEEN :fecha_inicio AND :fecha_fin
                    ORDER BY c.fecha_compra DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener compras por fecha: " . $e->getMessage());
            return [];
        }
    }
}
?>
