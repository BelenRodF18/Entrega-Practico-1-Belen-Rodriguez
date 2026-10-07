# Glosario de datos

---

**entidad:** roles
**Tipo entidad:** Maestra
Se albergan los roles que puede tener una persona dentro del sistema.
*[id_rol (int ai), nombre_rol (varchar(30) not null unique)]*

---

**entidad:** categorias
**Tipo entidad:** Maestra
Se albergan las categorías del catálogo (Libro, Revista, Audiolibro), y el texto del botón de cada tarjeta (Leer / Escuchar).
*[id_categoria (int ai), nombre_categoria (varchar(50) not null unique), accion (varchar(20) not null)]*

---

**entidad:** autores
**Tipo entidad:** Maestra
Se albergan los autores de los artículos de la biblioteca.
*[id_autor (int ai), nombre_autor (varchar(100) not null), nacionalidad (varchar(50) null)]*

---

**entidad:** editoriales
**Tipo entidad:** Maestra
Se albergan las editoriales que publican los artículos de la biblioteca.
*[id_editorial (int ai), nombre_editorial (varchar(100) not null), pais (varchar(50) null)]*

---

**entidad:** usuarios
**Tipo entidad:** Subordinada (roles, categorias)
Se albergan los datos de las personas registradas en la biblioteca, sus datos de acceso y la categoría que les interesa.
*[id_usuario (int ai), nombre (varchar(100) not null), email (varchar(100) not null unique), password_hash (varchar(255) not null), id_rol (int foranea roles:id_rol), id_categoria_interes (int null foranea categorias:id_categoria), fecha_registro (datetime default current_timestamp)]*

---

**entidad:** articulos
**Tipo entidad:** Subordinada (categorias, autores, editoriales)
Se albergan los artículos del catálogo de la biblioteca (libros, revistas y audiolibros).
*[id_articulo (int ai), titulo (varchar(150) not null), descripcion (text null), imagen (varchar(255) null), anio_publicacion (int null), disponible (tinyint(1) not null default 1), id_categoria (int foranea categorias:id_categoria), id_autor (int foranea autores:id_autor), id_editorial (int null foranea editoriales:id_editorial)]*

---

**entidad:** prestamos
**Tipo entidad:** Subordinada (usuarios, articulos)
Se albergan los préstamos de artículos que realizan los usuarios, con sus fechas y estado.
*[id_prestamo (int ai), id_usuario (int foranea usuarios:id_usuario), id_articulo (int foranea articulos:id_articulo), fecha_prestamo (date not null), fecha_devolucion (date null), estado (varchar(20) not null default 'Activo')]*
