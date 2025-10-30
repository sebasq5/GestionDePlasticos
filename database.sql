-- ====================================================
-- Base de Datos: plastico_db
-- Sistema de Gestión de Compras de Plástico
-- ====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS plastico_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE plastico_db;

-- ====================================================
-- Tabla: usuarios
-- Almacena información de usuarios del sistema
-- ====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado', 'proveedor') DEFAULT 'empleado',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- Tabla: proveedores
-- Almacena información de proveedores de plástico
-- ====================================================
CREATE TABLE IF NOT EXISTS proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    ruc VARCHAR(13) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- Tabla: compras
-- Registra las compras de plástico realizadas
-- ====================================================
CREATE TABLE IF NOT EXISTS compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_proveedor INT NOT NULL,
    fecha_compra DATE NOT NULL,
    tipo_plastico VARCHAR(100) NOT NULL,
    cantidad_kg DECIMAL(10,2) NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) GENERATED ALWAYS AS (cantidad_kg * costo_unitario) STORED,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- Datos de Prueba
-- ====================================================

-- Insertar usuarios de prueba (contraseña: 12345)
INSERT INTO usuarios (nombre, correo, contraseña_hash, rol, estado) VALUES
('Administrador del Sistema', 'admin@plasticos.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe6.r7hHm3N3T0v3zA3WzJl5YP7l7AYvG', 'admin', 'activo'),
('Juan Pérez', 'empleado@plasticos.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe6.r7hHm3N3T0v3zA3WzJl5YP7l7AYvG', 'empleado', 'activo'),
('María González', 'maria@proveedor.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe6.r7hHm3N3T0v3zA3WzJl5YP7l7AYvG', 'proveedor', 'activo');

-- Insertar proveedores de prueba
INSERT INTO proveedores (nombre_empresa, ruc, telefono, direccion) VALUES
('PlastiCorp S.A.', '1234567890001', '0998765432', 'Av. Principal #123, Guayaquil'),
('Reciclajes del Ecuador', '0987654321001', '0987654321', 'Calle Secundaria #456, Quito'),
('Polímeros Nacionales', '1122334455001', '0991234567', 'Zona Industrial #789, Cuenca');

-- Insertar compras de prueba
INSERT INTO compras (id_usuario, id_proveedor, fecha_compra, tipo_plastico, cantidad_kg, costo_unitario, observaciones) VALUES
(2, 1, '2025-10-15', 'PET', 150.50, 0.80, 'Primera compra del mes'),
(2, 2, '2025-10-20', 'HDPE', 200.00, 0.75, 'Material de alta calidad'),
(2, 1, '2025-10-25', 'PVC', 100.25, 0.90, 'Entrega inmediata');

-- ====================================================
-- Índices para mejorar el rendimiento
-- ====================================================
CREATE INDEX idx_usuarios_correo ON usuarios(correo);
CREATE INDEX idx_usuarios_rol ON usuarios(rol);
CREATE INDEX idx_proveedores_ruc ON proveedores(ruc);
CREATE INDEX idx_compras_fecha ON compras(fecha_compra);
CREATE INDEX idx_compras_usuario ON compras(id_usuario);
CREATE INDEX idx_compras_proveedor ON compras(id_proveedor);

-- ====================================================
-- Información de conexión
-- ====================================================
-- Usuario: admin@plasticos.com | Contraseña: 12345
-- Usuario: empleado@plasticos.com | Contraseña: 12345
-- Usuario: maria@proveedor.com | Contraseña: 12345
