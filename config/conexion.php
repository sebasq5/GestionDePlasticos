<?php
/**
 * Archivo de Configuración de Conexión a Base de Datos
 * 
 * Este archivo establece la conexión con MySQL usando PDO (PHP Data Objects)
 * PDO es más seguro que mysqli y permite prepared statements para prevenir SQL Injection
 * 
 * @author Sistema de Gestión de Plásticos
 * @version 1.0
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');        // Servidor de base de datos
define('DB_PORT', '3307');             // Puerto de MySQL en Laragon
define('DB_NAME', 'plastico_db');      // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario de MySQL
define('DB_PASS', '');                 // Contraseña (vacía por defecto en Laragon)
define('DB_CHARSET', 'utf8mb4');       // Codificación UTF-8

/**
 * Clase Database
 * Maneja la conexión a la base de datos usando el patrón Singleton
 */
class Database {
    private static $instance = null;
    private $conexion;
    
    /**
     * Constructor privado para implementar Singleton
     * Previene la creación de múltiples instancias
     */
    private function __construct() {
        try {
            // DSN (Data Source Name) - Cadena de conexión con puerto personalizado
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Opciones de PDO para mayor seguridad y funcionalidad
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Lanza excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Retorna arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                     // Usa prepared statements reales
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"        // Codificación UTF-8
            ];
            
            // Crear conexión PDO
            $this->conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            
        } catch (PDOException $e) {
            // Registrar error y mostrar mensaje genérico
            error_log("Error de conexión: " . $e->getMessage());
            die("Error al conectar con la base de datos. Por favor contacte al administrador.");
        }
    }
    
    /**
     * Obtiene la instancia única de Database
     * 
     * @return Database Instancia única de la clase
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO Objeto de conexión PDO
     */
    public function getConexion() {
        return $this->conexion;
    }
    
    /**
     * Previene la clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Previene la deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton.");
    }
}

/**
 * Función helper para obtener la conexión fácilmente
 * 
 * @return PDO Objeto de conexión PDO
 */
function getDB() {
    return Database::getInstance()->getConexion();
}
?>
