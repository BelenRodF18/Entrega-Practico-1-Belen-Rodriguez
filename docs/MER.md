# Modelo Entidad-Relación (MER)

La landing del Práctico 1 tiene un catálogo de artículos (libros, revistas y audiolibros) con título, autor, categoría e imagen. Los usuarios se registran con nombre, correo, contraseña e interés, inician sesión y tienen un rol (Administrador o Usuario). Además los usuarios piden prestados los artículos de la biblioteca.

## Diagrama

```mermaid
erDiagram
    ROLES ||--o{ USUARIOS : "tiene"
    CATEGORIAS ||--o{ USUARIOS : "le interesa"
    CATEGORIAS ||--o{ ARTICULOS : "clasifica"
    AUTORES ||--o{ ARTICULOS : "escribe"
    EDITORIALES |o--o{ ARTICULOS : "publica"
    USUARIOS ||--o{ PRESTAMOS : "realiza"
    ARTICULOS ||--o{ PRESTAMOS : "es prestado en"

    ROLES {
        int id_rol PK
        varchar nombre_rol
    }
    USUARIOS {
        int id_usuario PK
        varchar nombre
        varchar email
        varchar password_hash
        datetime fecha_registro
    }
    CATEGORIAS {
        int id_categoria PK
        varchar nombre_categoria
        varchar accion
    }
    AUTORES {
        int id_autor PK
        varchar nombre_autor
        varchar nacionalidad
    }
    EDITORIALES {
        int id_editorial PK
        varchar nombre_editorial
        varchar pais
    }
    ARTICULOS {
        int id_articulo PK
        varchar titulo
        text descripcion
        varchar imagen
        int anio_publicacion
        tinyint disponible
    }
    PRESTAMOS {
        int id_prestamo PK
        date fecha_prestamo
        date fecha_devolucion
        varchar estado
    }
```

PRESTAMOS es la relación N:N entre USUARIOS y ARTICULOS y tiene atributos propios (fechas y estado).

## Entidades y atributos

| Entidad | Clave primaria | Atributos |
|---|---|---|
| **ROLES** | id_rol | nombre_rol |
| **USUARIOS** | id_usuario | nombre, email, password_hash, fecha_registro |
| **CATEGORIAS** | id_categoria | nombre_categoria, accion |
| **AUTORES** | id_autor | nombre_autor, nacionalidad |
| **EDITORIALES** | id_editorial | nombre_editorial, pais |
| **ARTICULOS** | id_articulo | titulo, descripcion, imagen, anio_publicacion, disponible |
| **PRESTAMOS** | id_prestamo | fecha_prestamo, fecha_devolucion, estado |

## Relaciones y cardinalidades

| Relación | Entidades | Cardinalidad | Lectura |
|---|---|---|---|
| tiene | ROLES y USUARIOS | 1 : N | Un rol lo tienen muchos usuarios; cada usuario tiene un solo rol. |
| le interesa | CATEGORIAS y USUARIOS | 1 : N | Una categoría puede interesarle a muchos usuarios; cada usuario elige como máximo una. |
| clasifica | CATEGORIAS y ARTICULOS | 1 : N | Una categoría agrupa muchos artículos; cada artículo pertenece a una categoría. |
| escribe | AUTORES y ARTICULOS | 1 : N | Un autor escribe muchos artículos; cada artículo tiene un autor. |
| publica | EDITORIALES y ARTICULOS | 1 : N | Una editorial publica muchos artículos; un artículo puede tener una o ninguna editorial. |
| pide prestado (PRESTAMOS) | USUARIOS y ARTICULOS | N : M | Un usuario puede pedir muchos artículos y un artículo puede ser prestado a muchos usuarios a lo largo del tiempo. |
