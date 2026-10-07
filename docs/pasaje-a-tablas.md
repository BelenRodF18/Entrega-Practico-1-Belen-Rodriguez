# Pasaje a tablas (Modelo lógico)

PK = clave primaria, FK = clave foránea

```
ROLES       ( PK id_rol, nombre_rol )

CATEGORIAS  ( PK id_categoria, nombre_categoria, accion )

AUTORES     ( PK id_autor, nombre_autor, nacionalidad )

EDITORIALES ( PK id_editorial, nombre_editorial, pais )

USUARIOS    ( PK id_usuario, nombre, email, password_hash,
              FK id_rol -> ROLES(id_rol),
              FK id_categoria_interes -> CATEGORIAS(id_categoria),
              fecha_registro )

ARTICULOS   ( PK id_articulo, titulo, descripcion, imagen, anio_publicacion, disponible,
              FK id_categoria -> CATEGORIAS(id_categoria),
              FK id_autor -> AUTORES(id_autor),
              FK id_editorial -> EDITORIALES(id_editorial) )

PRESTAMOS   ( PK id_prestamo,
              FK id_usuario -> USUARIOS(id_usuario),
              FK id_articulo -> ARTICULOS(id_articulo),
              fecha_prestamo, fecha_devolucion, estado )
```

## Cómo se resolvió cada relación

| Relación del MER | Cardinalidad | Cómo queda en tablas |
|---|---|---|
| ROLES tiene USUARIOS | 1 : N | La PK de ROLES pasa como FK `id_rol` a USUARIOS. NOT NULL porque todo usuario tiene rol. |
| CATEGORIAS le interesa a USUARIOS | 1 : N | FK `id_categoria_interes` en USUARIOS. Admite NULL (el interés es opcional). |
| CATEGORIAS clasifica ARTICULOS | 1 : N | FK `id_categoria` en ARTICULOS. NOT NULL. |
| AUTORES escribe ARTICULOS | 1 : N | FK `id_autor` en ARTICULOS. NOT NULL. |
| EDITORIALES publica ARTICULOS | 1 : N | FK `id_editorial` en ARTICULOS. Admite NULL. |
| USUARIOS pide prestado ARTICULOS | N : M | Se crea la tabla intermedia PRESTAMOS con su propia PK, las dos FK (`id_usuario`, `id_articulo`) y los atributos de la relación (fechas y estado). |

## Restricciones

- `ROLES.nombre_rol`, `CATEGORIAS.nombre_categoria` y `USUARIOS.email` son UNIQUE.
- `USUARIOS.password_hash` guarda el hash de la contraseña (`password_hash()` de PHP).
- `ARTICULOS.disponible` vale 1 (disponible) o 0 (no disponible). Por defecto 1.
- `PRESTAMOS.estado` vale 'Activo' o 'Devuelto'; `fecha_devolucion` queda en NULL mientras el préstamo está activo.
- Orden de creación: ROLES, CATEGORIAS, AUTORES, EDITORIALES, USUARIOS, ARTICULOS, PRESTAMOS.

El script de la base está en [`api/config/init.sql`](../api/config/init.sql).
