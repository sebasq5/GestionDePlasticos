<?php
/**
 * Controlador: Compra
 * 
 * Maneja las peticiones y lógica de negocio para compras
 * Valida datos y coordina entre vistas y modelo
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

require_once __DIR__ . '/../models/Compra.php';

class CompraController {
    private $modelo;
    
    /**
     * Constructor - Inicializa el modelo
     */
    public function __construct() {
        $this->modelo = new Compra();
    }
    
    /**
     * Lista todas las compras
     * 
     * @param int|null $idProveedor Filtrar por proveedor (opcional)
     * @return array Lista de compras
     */
    public function listar($idProveedor = null) {
        return $this->modelo->obtenerTodos($idProveedor);
    }
    
    /**
     * Obtiene una compra por ID
     * 
     * @param int $id ID de la compra
     * @return array|false Datos de la compra
     */
    public function obtener($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return false;
        }
        return $this->modelo->obtenerPorId($id);
    }
    
    /**
     * Crea una nueva compra
     * 
     * @param array $datos Datos de la compra
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
        $datos['tipo_plastico'] = filter_var($datos['tipo_plastico'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['cantidad_kg'] = filter_var($datos['cantidad_kg'], FILTER_VALIDATE_FLOAT);
        $datos['costo_unitario'] = filter_var($datos['costo_unitario'], FILTER_VALIDATE_FLOAT);
        
        if (!empty($datos['observaciones'])) {
            $datos['observaciones'] = filter_var($datos['observaciones'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        // Intentar crear
        if ($this->modelo->crear($datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Compra registrada exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al registrar la compra'
            ];
        }
    }
    
    /**
     * Actualiza una compra existente
     * 
     * @param int $id ID de la compra
     * @param array $datos Datos a actualizar
     * @return array Resultado de la operación
     */
    public function actualizar($id, $datos) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de compra inválido'
            ];
        }
        
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
        $datos['tipo_plastico'] = filter_var($datos['tipo_plastico'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['cantidad_kg'] = filter_var($datos['cantidad_kg'], FILTER_VALIDATE_FLOAT);
        $datos['costo_unitario'] = filter_var($datos['costo_unitario'], FILTER_VALIDATE_FLOAT);
        
        if (!empty($datos['observaciones'])) {
            $datos['observaciones'] = filter_var($datos['observaciones'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        
        // Intentar actualizar
        if ($this->modelo->actualizar($id, $datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Compra actualizada exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar la compra'
            ];
        }
    }
    
    /**
     * Elimina una compra
     * 
     * @param int $id ID de la compra
     * @return array Resultado de la operación
     */
    public function eliminar($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de compra inválido'
            ];
        }
        
        if ($this->modelo->eliminar($id)) {
            return [
                'exito' => true,
                'mensaje' => 'Compra eliminada exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al eliminar la compra'
            ];
        }
    }
    
    /**
     * Obtiene estadísticas de compras
     * 
     * @return array Estadísticas
     */
    public function obtenerEstadisticas() {
        return $this->modelo->obtenerEstadisticas();
    }
    
    /**
     * Valida los datos de una compra
     * 
     * @param array $datos Datos a validar
     * @return array Errores encontrados
     */
    private function validarDatos($datos) {
        $errores = [];
        
        // Validar proveedor
        $idProveedor = filter_var($datos['id_proveedor'], FILTER_VALIDATE_INT);
        if (!$idProveedor || $idProveedor <= 0) {
            $errores[] = 'Debe seleccionar un proveedor válido';
        }
        
        // Validar fecha de compra
        if (empty($datos['fecha_compra'])) {
            $errores[] = 'La fecha de compra es requerida';
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_compra']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_compra']) {
                $errores[] = 'La fecha de compra no es válida';
            }
        }
        
        // Validar tipo de plástico
        if (empty($datos['tipo_plastico']) || strlen($datos['tipo_plastico']) < 2) {
            $errores[] = 'El tipo de plástico debe tener al menos 2 caracteres';
        }
        
        // Validar cantidad
        $cantidad = filter_var($datos['cantidad_kg'], FILTER_VALIDATE_FLOAT);
        if ($cantidad === false || $cantidad <= 0) {
            $errores[] = 'La cantidad en kg debe ser un número positivo';
        } elseif ($cantidad > 99999999.99) {
            $errores[] = 'La cantidad excede el límite permitido';
        }
        
        // Validar costo unitario
        $costo = filter_var($datos['costo_unitario'], FILTER_VALIDATE_FLOAT);
        if ($costo === false || $costo <= 0) {
            $errores[] = 'El costo unitario debe ser un número positivo';
        } elseif ($costo > 99999999.99) {
            $errores[] = 'El costo unitario excede el límite permitido';
        }
        
        return $errores;
    }
}
?>
