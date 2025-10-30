# 📘 Guía de Instalación Detallada
## Sistema de Gestión de Plásticos

Esta guía paso a paso le ayudará a instalar y configurar el sistema correctamente.

---

## 🔧 Paso 1: Preparar el Entorno

### Opción A: Usando Laragon (Recomendado)

1. **Descargar Laragon**
   - Visite: https://laragon.org/download/
   - Descargue la versión "Laragon Full"
   - Instale siguiendo el asistente

2. **Verificar instalación**
   - Abra Laragon
   - Haga clic en "Start All"
   - Debe ver Apache y MySQL en verde (activos)

3. **Ubicar el proyecto**
   - Copie la carpeta `plasticos` en: `C:\laragon\www\`
   - Ruta final: `C:\laragon\www\plasticos`

### Opción B: Usando XAMPP

1. **Descargar XAMPP**
   - Visite: https://www.apachefriends.org/
   - Descargue la versión para Windows
   - Instale en la ubicación predeterminada

2. **Iniciar servicios**
   - Abra XAMPP Control Panel
   - Inicie Apache y MySQL

3. **Ubicar el proyecto**
   - Copie la carpeta `plasticos` en: `C:\xampp\htdocs\`
   - Ruta final: `C:\xampp\htdocs\plasticos`

---

## 🗄️ Paso 2: Configurar la Base de Datos

### Método 1: Usando phpMyAdmin (Recomendado)

1. **Acceder a phpMyAdmin**
   ```
   http://localhost/phpmyadmin
   ```

2. **Crear la base de datos**
   - Haga clic en "Nueva" en el panel izquierdo
   - Nombre: `plastico_db`
   - Cotejamiento: `utf8mb4_unicode_ci`
   - Clic en "Crear"

3. **Importar el archivo SQL**
   - Seleccione la base de datos `plastico_db`
   - Vaya a la pestaña "Importar"
   - Haga clic en "Seleccionar archivo"
   - Busque y seleccione: `C:\laragon\www\plasticos\database.sql`
   - Haga clic en "Continuar" al final de la página

4. **Verificar importación**
   - Debe ver 3 tablas: `usuarios`, `proveedores`, `compras`
   - Cada tabla debe tener datos de prueba

### Método 2: Usando MySQL Command Line

```sql
-- Abra MySQL Command Line o ejecute desde Laragon Terminal

-- Crear base de datos
CREATE DATABASE plastico_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE plastico_db;

-- Importar archivo
SOURCE C:/laragon/www/plasticos/database.sql;

-- Verificar
SHOW TABLES;
```

---

## ⚙️ Paso 3: Configurar la Conexión

1. **Abrir archivo de configuración**
   - Ruta: `plasticos/config/conexion.php`
   - Use un editor de texto (Visual Studio Code, Notepad++, Sublime Text)

2. **Verificar/Modificar credenciales**

   ```php
   // CONFIGURACIÓN PARA LARAGON (Puerto 3307)
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3307');      // Puerto de Laragon
   define('DB_NAME', 'plastico_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');          // Sin contraseña por defecto
   ```

   **Si usa XAMPP (Puerto 3306):**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');      // Puerto de XAMPP
   define('DB_NAME', 'plastico_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
   
   **Si usa otro servidor:**
   ```php
   define('DB_HOST', '127.0.0.1');      // o IP de su servidor
   define('DB_PORT', '3306');           // Puerto MySQL
   define('DB_USER', 'su_usuario');
   define('DB_PASS', 'su_contraseña');
   ```

3. **Guardar cambios**

---

## 🌐 Paso 4: Probar el Sistema

1. **Acceder al sistema**
   ```
   http://localhost/plasticos
   ```

2. **Iniciar sesión**
   
   **Como Administrador:**
   - Correo: `admin@plasticos.com`
   - Contraseña: `12345`
   
   **Como Empleado:**
   - Correo: `empleado@plasticos.com`
   - Contraseña: `12345`
   
   **Como Proveedor:**
   - Correo: `maria@proveedor.com`
   - Contraseña: `12345`

3. **Explorar funcionalidades**
   - Dashboard personalizado según el rol
   - Menú de navegación
   - CRUD de cada módulo

---

## ✅ Paso 5: Verificaciones

### Verificar PHP
```bash
# En terminal o CMD
php -v
# Debe mostrar PHP 8.0 o superior
```

### Verificar extensiones PHP necesarias
```bash
php -m
# Busque estas extensiones:
# - pdo_mysql
# - mysqli
# - mbstring
# - openssl
```

### Verificar MySQL
```bash
mysql --version
# Debe mostrar MySQL 5.7 o superior
```

---

## 🐛 Solución de Problemas Comunes

### Error: "No se puede conectar a la base de datos"

**Solución 1:** Verificar que MySQL esté corriendo
- Laragon: Verificar que MySQL esté en verde
- XAMPP: Verificar en XAMPP Control Panel

**Solución 2:** Verificar credenciales
```php
// Imprimir errores temporalmente en conexion.php
try {
    $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Error: "Página en blanco"

**Activar errores PHP:**
```php
// Agregar al inicio de index.php temporalmente
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Error: "No se encuentran los estilos CSS"

**Verificar rutas:**
- Revisar que las rutas en `includes/funciones.php` sean correctas
- Verificar que la carpeta `assets` esté en la raíz del proyecto

### Error: "Sesión no funciona"

**Verificar configuración PHP:**
```ini
; En php.ini
session.save_path = "/tmp"
session.gc_probability = 1
session.gc_divisor = 100
```

---

## 🔒 Paso 6: Cambiar Contraseñas (Producción)

**IMPORTANTE:** Antes de usar en producción, cambie las contraseñas.

### Generar hash de contraseña:
```php
<?php
// Crear archivo temporal: generar_hash.php
$contraseña = "MiContraseñaSegura123!";
echo password_hash($contraseña, PASSWORD_DEFAULT);
?>
```

### Actualizar en base de datos:
```sql
UPDATE usuarios 
SET contraseña_hash = '$2y$10$...' 
WHERE correo = 'admin@plasticos.com';
```

---

## 📊 Paso 7: Cargar Datos Propios

### Agregar proveedores reales:
```sql
INSERT INTO proveedores (nombre_empresa, ruc, telefono, direccion) 
VALUES ('Mi Empresa S.A.', '1234567890001', '0999999999', 'Calle Principal #123');
```

### Agregar usuarios:
```php
// Usar el formulario de crear usuario en el sistema
// O ejecutar SQL:
INSERT INTO usuarios (nombre, correo, contraseña_hash, rol, estado)
VALUES ('Juan Pérez', 'juan@empresa.com', '$2y$10$...', 'empleado', 'activo');
```

---

## 🚀 Paso 8: Optimización (Opcional)

### Habilitar compresión GZIP
```apache
# En .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

### Configurar caché de navegador
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

---

## 📱 Acceso desde otros dispositivos (Red local)

1. **Obtener IP de su PC:**
   ```cmd
   ipconfig
   # Busque "Dirección IPv4": ej. 192.168.1.100
   ```

2. **Acceder desde otro dispositivo:**
   ```
   http://192.168.1.100/plasticos
   ```

3. **Configurar firewall (si es necesario):**
   - Permitir puerto 80 (Apache)
   - Permitir puerto 3306 (MySQL) si es necesario

---

## 📚 Recursos Adicionales

- **Documentación PHP PDO:** https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 5:** https://getbootstrap.com/docs/5.3/
- **MySQL:** https://dev.mysql.com/doc/
- **Font Awesome:** https://fontawesome.com/icons

---

## 💾 Respaldo de Datos

### Exportar base de datos:
```bash
# Desde phpMyAdmin: Exportar > SQL
# O desde terminal:
mysqldump -u root plastico_db > backup_$(date +%Y%m%d).sql
```

### Restaurar:
```bash
mysql -u root plastico_db < backup_20251030.sql
```

---

## 📞 Soporte

Si tiene problemas:
1. Revise esta guía completamente
2. Verifique los logs de errores de Apache y PHP
3. Consulte los comentarios en el código fuente

---

**¡Listo! Su sistema debería estar funcionando correctamente.** 🎉
