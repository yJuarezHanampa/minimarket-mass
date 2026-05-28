-- ============================================================
-- SISTEMA DE INVENTARIO MINIMARKET MASS
-- Backend Developer Web - SENATI Arequipa 2026
-- ============================================================
-- Base de datos completa para todo el módulo.
-- Se irá usando incrementalmente en las próximas sesiones:
--   Sesión 2 (hoy): categorias, productos
--   Sesión 3-4: clientes, usuarios
--   Sesión 5-6: ventas, detalle_ventas, sesiones
--   Sesión 7+: el sistema completo
-- ============================================================

DROP DATABASE IF EXISTS minimarket_mass;
CREATE DATABASE minimarket_mass CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE minimarket_mass;

-- ============================================================
-- TABLA: categorias
-- ============================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(200),
    pasillo VARCHAR(20),
    igv_aplica BOOLEAN DEFAULT TRUE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categorias (nombre, descripcion, pasillo, igv_aplica) VALUES
('Abarrotes', 'Arroz, fideos, aceite, conservas', 'Pasillo 1', TRUE),
('Bebidas', 'Gaseosas, agua, jugos, energizantes', 'Pasillo 2', TRUE),
('Lacteos', 'Leche, yogurt, queso, mantequilla', 'Pasillo 3', TRUE),
('Limpieza', 'Detergente, lavavajillas, lejía', 'Pasillo 4', TRUE),
('Aseo Personal', 'Shampoo, jabón, pasta dental', 'Pasillo 5', TRUE),
('Panaderia', 'Pan, galletas, dulces', 'Pasillo 6', FALSE),
('Frutas y Verduras', 'Frutas y verduras frescas', 'Zona fresca', FALSE);

-- ============================================================
-- TABLA: productos
-- ============================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(20) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(50),
    categoria_id INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 10,
    unidad_medida VARCHAR(20),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

INSERT INTO productos (codigo_barras, nombre, marca, categoria_id, precio, stock, unidad_medida) VALUES
-- Abarrotes
('7750243012345', 'Arroz Costeño 750g', 'Costeño', 1, 4.20, 120, 'bolsa'),
('7750243023456', 'Aceite Primor 1L', 'Primor', 1, 8.90, 85, 'botella'),
('7750243034567', 'Fideos Don Vittorio 250g', 'Don Vittorio', 1, 3.50, 95, 'paquete'),
('7750243045678', 'Atun Florida 170g', 'Florida', 1, 5.80, 60, 'lata'),
('7750243056789', 'Azucar Rubia Cartavio 1kg', 'Cartavio', 1, 4.50, 110, 'bolsa'),
-- Bebidas
('7750182112345', 'Inca Kola 1.5L', 'Inca Kola', 2, 6.50, 200, 'botella'),
('7750182123456', 'Inca Kola 500ml', 'Inca Kola', 2, 3.00, 180, 'botella'),
('7750182134567', 'Coca Cola 1.5L', 'Coca Cola', 2, 7.00, 150, 'botella'),
('7750182145678', 'Agua San Luis 625ml', 'San Luis', 2, 1.50, 250, 'botella'),
('7750182156789', 'Cifrut Naranja 500ml', 'Cifrut', 2, 2.00, 140, 'botella'),
-- Lacteos
('7750355212345', 'Leche Gloria Evaporada 410g', 'Gloria', 3, 4.20, 180, 'lata'),
('7750355223456', 'Yogurt Gloria Fresa 1L', 'Gloria', 3, 9.50, 70, 'botella'),
('7750355234567', 'Queso Laive 200g', 'Laive', 3, 12.00, 45, 'paquete'),
-- Limpieza
('7750488312345', 'Detergente Ace 780g', 'Ace', 4, 9.90, 90, 'bolsa'),
('7750488323456', 'Lejia Clorox 1L', 'Clorox', 4, 4.50, 75, 'botella'),
('7750488334567', 'Lavavajilla Sapolio', 'Sapolio', 4, 3.20, 100, 'unidad'),
-- Aseo personal
('7750521412345', 'Shampoo Pantene 200ml', 'Pantene', 5, 12.50, 60, 'botella'),
('7750521423456', 'Jabon Bolivar', 'Bolivar', 5, 2.50, 150, 'unidad'),
('7750521434567', 'Pasta Colgate 75ml', 'Colgate', 5, 5.80, 80, 'unidad'),
-- Panaderia
('7750654512345', 'Pan Frances', 'Mass', 6, 0.30, 300, 'unidad'),
('7750654523456', 'Galletas Soda Field', 'Field', 6, 1.50, 200, 'paquete'),
-- Frutas
('7750787612345', 'Platano de Seda', 'Local', 7, 0.50, 200, 'unidad'),
('7750787623456', 'Manzana Roja', 'Importado', 7, 1.20, 150, 'unidad');

-- ============================================================
-- TABLA: clientes  (uso desde sesion 3)
-- ============================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(8) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion VARCHAR(200),
    fecha_nacimiento DATE,
    puntos_acumulados INT DEFAULT 0,
    tipo_cliente ENUM('regular', 'frecuente', 'vip') DEFAULT 'regular',
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO clientes (dni, nombres, apellidos, telefono, email, tipo_cliente) VALUES
('74521893', 'Carlos Eduardo', 'Mamani Quispe', '987654321', 'carlos.mamani@gmail.com', 'frecuente'),
('45678912', 'Maria Elena', 'Garcia Lopez', '976543210', 'maria.garcia@gmail.com', 'vip'),
('12345678', 'Jose Antonio', 'Quispe Huaman', '965432109', 'jose.quispe@gmail.com', 'regular'),
('87654321', 'Rosa Maria', 'Flores Choque', '954321098', 'rosa.flores@gmail.com', 'frecuente'),
('98765432', 'Juan Carlos', 'Rodriguez Perez', '943210987', 'juan.rodriguez@gmail.com', 'regular');

-- ============================================================
-- TABLA: usuarios  (uso desde sesion 6 - login)
-- ============================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('admin', 'cajero', 'almacen') DEFAULT 'cajero',
    tienda VARCHAR(50) DEFAULT 'Mass Cayma',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL
);

-- Password de prueba: "admin123" (hasheado en sesion 6)
INSERT INTO usuarios (username, password_hash, nombres, apellidos, email, rol) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'admin@mass.pe', 'admin'),
('cajero01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Lopez', 'pedro.lopez@mass.pe', 'cajero'),
('cajero02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Torres', 'ana.torres@mass.pe', 'cajero');

-- ============================================================
-- TABLA: ventas  (uso desde sesion 5 - PDO)
-- ============================================================
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_comprobante VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT,
    usuario_id INT NOT NULL,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    igv DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'yape', 'plin', 'tarjeta') NOT NULL,
    estado ENUM('completada', 'anulada') DEFAULT 'completada',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ============================================================
-- TABLA: detalle_ventas  (uso desde sesion 5 - PDO)
-- ============================================================
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- ============================================================
-- Verificación
-- ============================================================
-- Resumen de la base de datos creada
SELECT 'BD minimarket_mass creada exitosamente' AS mensaje;
SELECT COUNT(*) AS total_categorias FROM categorias;
SELECT COUNT(*) AS total_productos FROM productos;
SELECT COUNT(*) AS total_clientes FROM clientes;
SELECT COUNT(*) AS total_usuarios FROM usuarios;
