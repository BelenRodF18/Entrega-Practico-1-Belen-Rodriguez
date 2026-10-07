-- ========================================================
-- SCRIPT DE CREACIÓN: BIBLIODIGITAL (BIBLIOTECA VIRTUAL)
-- ========================================================
SET NAMES utf8mb4;

-- 1. CREACIÓN DE TABLAS (DDL)
CREATE TABLE ROLES (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE CATEGORIAS (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(50) NOT NULL UNIQUE,
    accion VARCHAR(20) NOT NULL
);

CREATE TABLE AUTORES (
    id_autor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_autor VARCHAR(100) NOT NULL,
    nacionalidad VARCHAR(50)
);

CREATE TABLE EDITORIALES (
    id_editorial INT AUTO_INCREMENT PRIMARY KEY,
    nombre_editorial VARCHAR(100) NOT NULL,
    pais VARCHAR(50)
);

CREATE TABLE USUARIOS (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    id_categoria_interes INT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES ROLES(id_rol),
    FOREIGN KEY (id_categoria_interes) REFERENCES CATEGORIAS(id_categoria)
);

CREATE TABLE ARTICULOS (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    anio_publicacion INT,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    id_categoria INT NOT NULL,
    id_autor INT NOT NULL,
    id_editorial INT NULL,
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIAS(id_categoria),
    FOREIGN KEY (id_autor) REFERENCES AUTORES(id_autor),
    FOREIGN KEY (id_editorial) REFERENCES EDITORIALES(id_editorial)
);

CREATE TABLE PRESTAMOS (
    id_prestamo INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_articulo INT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario),
    FOREIGN KEY (id_articulo) REFERENCES ARTICULOS(id_articulo)
);

-- ========================================================
-- 2. POBLACIÓN DE DATOS (DML - INSERTS)
-- ========================================================

INSERT INTO ROLES (nombre_rol) VALUES
('Administrador'),
('Usuario');

INSERT INTO CATEGORIAS (nombre_categoria, accion) VALUES
('Libro', 'Leer'),
('Revista', 'Leer'),
('Audiolibro', 'Escuchar');

INSERT INTO AUTORES (nombre_autor, nacionalidad) VALUES
('Carlo Collodi', 'Italia'),
('Blue Jeans', 'España'),
('Maria Gonzales', 'Uruguay');

INSERT INTO EDITORIALES (nombre_editorial, pais) VALUES
('Editorial Anaya', 'España'),
('Editorial Planeta', 'España'),
('Condé Nast', 'Estados Unidos');

-- Usuarios de prueba: admin123 y usuario123
INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_categoria_interes) VALUES
('Belén Rodríguez', 'admin@bibliodigital.com', '$2y$12$t8Cqmd6P9FxDwXE6E3IUKeRnws4m35FXvYS8x23uK2ySud1l8QumK', 1, NULL),
('Lector de Prueba', 'usuario@bibliodigital.com', '$2y$12$i.feG/ufrOZkefEHPtDCOeW4VUTJnUcWK.0zujhTSFf9tK4e4dGza', 2, 1);

INSERT INTO ARTICULOS (titulo, descripcion, imagen, anio_publicacion, disponible, id_categoria, id_autor, id_editorial) VALUES
('Las aventuras de Pinocho', 'Clásico de la literatura infantil sobre el muñeco de madera que quería ser un niño de verdad.', 'libro1.jpg', 1883, 1, 1, 1, 1),
('La chica invisible', 'Novela juvenil de misterio narrada en formato audiolibro.', 'audiolibro1.jpg', 2018, 1, 3, 2, 2),
('Revista Vogue', 'Revista de moda, tendencias y cultura.', 'revista1.jpg', 2024, 1, 2, 3, 3);

INSERT INTO PRESTAMOS (id_usuario, id_articulo, fecha_prestamo, fecha_devolucion, estado) VALUES
(2, 1, '2026-09-20', '2026-10-04', 'Devuelto'),
(2, 2, '2026-10-01', NULL, 'Activo');
