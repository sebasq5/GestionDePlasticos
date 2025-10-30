# Sistema de Gestión de Plásticos

Sistema web completo desarrollado en PHP puro con MySQL para la gestión de compras de plástico reciclado. Incluye control de usuarios, proveedores y registro de compras con diferentes niveles de acceso según roles.

## Características

- **Autenticación segura** con password_hash() y password_verify()
- **Control de acceso por roles**: Admin, Empleado, Proveedor
- **CRUD completo** para Usuarios, Proveedores y Compras
- **Interfaz moderna** con Bootstrap 5
- **PDO** para consultas seguras y prevención de SQL Injection
- **Validación de datos** con filter_input
- **Código documentado** y organizado con arquitectura MVC

## Requisitos del Sistema

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Servidor web Apache/Nginx
- Laragon, XAMPP o WAMP (recomendado para desarrollo)

## Instalación

### 1. Clonar o copiar el proyecto

Copie la carpeta `plasticos` en el directorio de su servidor web:
- **Laragon**: `C:\laragon\www\plasticos`
- **XAMPP**: `C:\xampp\htdocs\plasticos`
- **WAMP**: `C:\wamp64\www\plasticos`

### 2. Crear la base de datos

1. Abra phpMyAdmin o su gestor de MySQL
2. Importe el archivo `database.sql` ubicado en la raíz del proyecto
3. Este archivo creará:
   - La base de datos `plastico_db`
   - Las tablas necesarias (usuarios, proveedores, compras)
   - Datos de prueba iniciales

**Alternativa manual**:
```sql
-- Ejecute el contenido completo del archivo database.sql
```

### 3. Configurar la conexión

Abra el archivo `config/conexion.php` y verifique/modifique estos parámetros si es necesario:

```php
define('DB_HOST', 'localhost');        // Servidor MySQL
define('DB_PORT', '3307');             // Puerto MySQL (3307 para Laragon, 3306 para XAMPP)
define('DB_NAME', 'plastico_db');      // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario MySQL
define('DB_PASS', '');                 // Contraseña MySQL (vacía en desarrollo)
```

**Nota:** Laragon usa el puerto **3307** por defecto, mientras que XAMPP usa **3306**.

### 4. Acceder al sistema

Abra su navegador y vaya a:
```
http://localhost/plasticos
```

## Usuarios de Prueba

El sistema incluye 3 usuarios predefinidos con diferentes roles:

### Administrador
- **Correo**: admin@plasticos.com
- **Contraseña**: 12345
- **Permisos**: Acceso completo a todo el sistema

### Empleado
- **Correo**: empleado@plasticos.com
- **Contraseña**: 12345
- **Permisos**: Registrar compras y ver proveedores

### Proveedor
- **Correo**: maria@proveedor.com
- **Contraseña**: 12345
- **Permisos**: Ver sus datos y compras realizadas a su empresa

## Estructura del Proyecto

```
plasticos/
│
├── config/
│   └── conexion.php              # Conexión PDO a MySQL
│
├── models/
│   ├── Usuario.php               # Modelo de usuarios
│   ├── Proveedor.php             # Modelo de proveedores
│   └── Compra.php                # Modelo de compras
│
├── controllers/
│   ├── UsuarioController.php     # Lógica de usuarios
│   ├── ProveedorController.php   # Lógica de proveedores
│   └── CompraController.php      # Lógica de compras
│
├── views/
│   ├── includes/
│   │   └── funciones.php         # Funciones reutilizables
│   ├── login.php                 # Página de login
│   ├── dashboard_admin.php       # Panel administrador
│   ├── dashboard_empleado.php    # Panel empleado
│   ├── dashboard_proveedor.php   # Panel proveedor
│   ├── usuarios/
│   │   ├── listar.php
│   │   ├── crear.php
│   │   └── editar.php
│   ├── proveedores/
│   │   ├── listar.php
│   │   ├── crear.php
│   │   └── editar.php
│   └── compras/
│       ├── listar.php
│       ├── crear.php
│       └── editar.php
│
├── assets/
│   ├── css/
│   │   └── estilos.css           # Estilos personalizados
│   └── js/
│       └── scripts.js            # JavaScript personalizado
│
├── index.php                     # Punto de entrada
├── logout.php                    # Cerrar sesión
├── database.sql                  # Script de base de datos
└── README.md                     # Este archivo
```

## Seguridad Implementada

- Contraseñas encriptadas con `password_hash()`
- Validación de sesiones en cada página
- Prepared Statements PDO (prevención SQL Injection)
- Sanitización de inputs con `filter_var()`
- Validación de permisos por roles
- Protección CSRF básica

## Funcionalidades por Rol

### Administrador
- Gestión completa de usuarios (crear, editar, eliminar)
- Gestión completa de proveedores
- Gestión completa de compras
- Ver estadísticas del sistema
- Acceso a todos los módulos

### Empleado
- Registrar nuevas compras
- Ver lista de compras
- Ver lista de proveedores
- No puede editar/eliminar
- No puede gestionar usuarios

### Proveedor
- Ver información de su empresa
- Ver compras realizadas a su empresa
- Solo lectura (sin permisos de edición)

##  Características Técnicas

### Arquitectura
- Patrón **MVC** (Model-View-Controller)
- Patrón **Singleton** para la conexión a BD
- Separación de responsabilidades

### Base de Datos
- **Campo calculado** (total = cantidad_kg * costo_unitario)
- **Restricciones de integridad** con FOREIGN KEY
- **Índices** para optimización de consultas
- **Validación** de RUC único y correo único

### Frontend
- Bootstrap 5.3 (framework CSS)
- Font Awesome 6.4 (iconos)
- Diseño responsive (mobile-friendly)
- Interfaz intuitiva y moderna

##  Personalización

### Cambiar colores del tema
Edite `assets/css/estilos.css`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    /* Modifique según sus preferencias */
}
```

### Agregar nuevos tipos de plástico
Los tipos más comunes ya están sugeridos:
- PET (Polietileno Tereftalato)
- HDPE (Polietileno de Alta Densidad)
- PVC (Policloruro de Vinilo)
- LDPE (Polietileno de Baja Densidad)
- PP (Polipropileno)
- PS (Poliestireno)

## Base de Datos

### Tablas principales

**usuarios**
- id_usuario (PK)
- nombre
- correo (UNIQUE)
- contraseña_hash
- rol (admin, empleado, proveedor)
- estado (activo, inactivo)

**proveedores**
- id_proveedor (PK)
- nombre_empresa
- ruc (UNIQUE, 13 dígitos)
- telefono
- direccion

**compras**
- id_compra (PK)
- id_usuario (FK)
- id_proveedor (FK)
- fecha_compra
- tipo_plastico
- cantidad_kg
- costo_unitario
- total (CALCULATED)

## Solución de Problemas

### Error de conexión a BD
Verifique en `config/conexion.php`:
- Nombre de usuario y contraseña correctos
- Base de datos creada
- Servidor MySQL activo

### Sesión no funciona
Asegúrese de que:
- `session_start()` esté al inicio de cada archivo
- Los permisos de escritura en el directorio de sesiones sean correctos

### Estilos no se cargan
Verifique las rutas en los archivos:
- Las rutas deben ser relativas correctamente
- Bootstrap CDN debe estar accesible

## Recursos Educativos

Este proyecto está diseñado para estudiantes de Ingeniería en Sistemas e incluye:

- Código ampliamente comentado
- Buenas prácticas de programación
- Implementación de seguridad básica
- Arquitectura escalable y mantenible
- Ejemplos de validación y sanitización

## Licencia

Este proyecto es de uso educativo y puede ser modificado libremente.

## Soporte

Para dudas o problemas:
1. Revise la documentación en el código
2. Consulte los comentarios en cada archivo
3. Verifique la configuración de la base de datos

---

**Desarrollado por Franklin Sanmartin**

Fecha: Octubre 2025


