<?php
/**
 * Controlador: Proveedor
 * 
 * Maneja las peticiones y lógica de negocio para proveedores
 * Valida datos y coordina entre vistas y modelo
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {
    private $modelo;
    
    /**
     * Constructor - Inicializa el modelo
     */
    public function __construct() {
        $this->modelo = new Proveedor();
    }
    
    /**
     * Lista todos los proveedores
     * 
     * @return array Lista de proveedores
     */
    public function listar() {
        return $this->modelo->obtenerTodos();
    }
    
    /**
     * Obtiene un proveedor por ID
     * 
     * @param int $id ID del proveedor
     * @return array|false Datos del proveedor
     */
    public function obtener($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return false;
        }
        return $this->modelo->obtenerPorId($id);
    }
    
    /**
     * Crea un nuevo proveedor
     * 
     * @param array $datos Datos del proveedor
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
        $datos['nombre_empresa'] = filter_var($datos['nombre_empresa'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['ruc'] = preg_replace('/[^0-9]/', '', $datos['ruc']);
        $datos['telefono'] = filter_var($datos['telefono'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['direccion'] = filter_var($datos['direccion'], FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Intentar crear
        if ($this->modelo->crear($datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Proveedor creado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al crear proveedor. El RUC puede estar duplicado.'
            ];
        }
    }
    
    /**
     * Actualiza un proveedor existente
     * 
     * @param int $id ID del proveedor
     * @param array $datos Datos a actualizar
     * @return array Resultado de la operación
     */
    public function actualizar($id, $datos) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de proveedor inválido'
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
        $datos['nombre_empresa'] = filter_var($datos['nombre_empresa'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['ruc'] = preg_replace('/[^0-9]/', '', $datos['ruc']);
        $datos['telefono'] = filter_var($datos['telefono'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datos['direccion'] = filter_var($datos['direccion'], FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Intentar actualizar
        if ($this->modelo->actualizar($id, $datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Proveedor actualizado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar proveedor'
            ];
        }
    }
    
    /**
     * Elimina un proveedor
     * 
     * @param int $id ID del proveedor
     * @return array Resultado de la operación
     */
    public function eliminar($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return [
                'exito' => false,
                'mensaje' => 'ID de proveedor inválido'
            ];
        }
        
        // Verificar si tiene compras asociadas
        $totalCompras = $this->modelo->obtenerTotalCompras($id);
        if ($totalCompras > 0) {
            return [
                'exito' => false,
                'mensaje' => 'No se puede eliminar el proveedor porque tiene compras registradas'
            ];
        }
        
        if ($this->modelo->eliminar($id)) {
            return [
                'exito' => true,
                'mensaje' => 'Proveedor eliminado exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Error al eliminar proveedor'
            ];
        }
    }
    
    /**
     * Valida los datos de un proveedor
     * 
     * @param array $datos Datos a validar
     * @param int|null $idExcluir ID a excluir en validación de RUC
     * @return array Errores encontrados
     */
    private function validarDatos($datos, $idExcluir = null) {
        $errores = [];
        
        // Validar nombre de empresa
        if (empty($datos['nombre_empresa']) || strlen($datos['nombre_empresa']) < 3) {
            $errores[] = 'El nombre de la empresa debe tener al menos 3 caracteres';
        }
        
        // Validar RUC
        if (empty($datos['ruc'])) {
            $errores[] = 'El RUC es requerido';
        } else {
            $ruc = preg_replace('/[^0-9]/', '', $datos['ruc']);
            if (strlen($ruc) != 13) {
                $errores[] = 'El RUC debe tener exactamente 13 dígitos';
            } elseif ($this->modelo->existeRuc($ruc, $idExcluir)) {
                $errores[] = 'El RUC ya está registrado';
            }
        }
        
        // Validar teléfono (opcional pero si viene debe ser válido)
        if (!empty($datos['telefono'])) {
            $telefono = preg_replace('/[^0-9]/', '', $datos['telefono']);
            if (strlen($telefono) < 7 || strlen($telefono) > 15) {
                $errores[] = 'El teléfono debe tener entre 7 y 15 dígitos';
            }
        }
        
        // Validar dirección
        if (empty($datos['direccion']) || strlen($datos['direccion']) < 5) {
            $errores[] = 'La dirección debe tener al menos 5 caracteres';
        }
        
        return $errores;
    }
}
?>
