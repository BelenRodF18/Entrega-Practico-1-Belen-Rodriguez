# Prácticos 2 y 3: Biblioteca Virtual Fullstack (BiblioDigital)

Continuación del Práctico 1. Sobre la misma Landing Page de BiblioDigital se agregó la base de datos, una API en PHP con MVC, login con roles, el dashboard del administrador con la gestión de artículos y el dashboard del usuario. El catálogo de la landing ahora se carga desde la base de datos.

##  Tecnologías y Herramientas utilizadas
* **Diseño UI:** Figma
* **Maquetación:** HTML5 y CSS3
* **Estilos y Estructura Responsiva:** Bootstrap
* **Interactividad:** JavaScript
* **Backend:** PHP (MVC)
* **Base de datos:** MySQL
* **Entorno:** Docker
* **Control de versiones:** Git & GitHub

##  Base de datos
* MER: `docs/MER.md`
* Pasaje a tablas: `docs/pasaje-a-tablas.md`
* Glosario de datos: `docs/glosario-de-datos.md`
* Script de la base: `api/config/init.sql`

##  Cómo levantar el proyecto
```
docker compose up -d --build
```
* Landing: http://localhost:8080
* Login: http://localhost:8080/front/dist/paginas/login.html
* phpMyAdmin: http://localhost:8081 (root / root_password)

Si se modifica el `init.sql` hay que borrar el volumen con `docker compose down -v` y volver a levantar.

##  Usuarios de prueba
* **Administrador:** admin@bibliodigital.com / admin123
* **Usuario:** usuario@bibliodigital.com / usuario123

##  Práctico 2
* **Login:** el formulario manda el correo y la contraseña a `/api/login`. La contraseña se compara con el hash guardado en la base.
* **Roles:** al iniciar sesión se guarda el rol del usuario en la sesión de PHP.
* **Redirección según el rol:** el Administrador va a `dist/paginas/admin/dashboard.html` y el Usuario a `dist/paginas/user/inicio.html`.
* **Dashboard de Administración:** usuario conectado, cerrar sesión, menú, resumen, artículos y usuarios registrados.
* **Dashboard de Usuario:** perfil, sus préstamos (con opción de devolver) y el catálogo para pedir artículos.
* **Protección:** si no hay sesión o el rol no corresponde, los dashboards redirigen. Las rutas `/api/admin/...` responden 401 o 403.
* **Sesión en la landing:** si hay sesión iniciada, la landing muestra el nombre, el acceso al panel y cerrar sesión.

##  Práctico 3
* **Agregar artículo:** desde el dashboard, con título, categoría, autor, editorial, año, descripción, imagen y disponibilidad.
* **Modificar artículo:** el botón Editar abre el formulario con los datos cargados.
* **Eliminar artículo:** el botón Eliminar pide confirmación y borra el artículo y sus préstamos.
* **Catálogo dinámico:** la landing obtiene los artículos de `/api/articulos`, así que los cambios del administrador se ven en la landing.
* Las imágenes subidas se guardan en `front/dist/assets/imagenes/`.

##  Estructura del Proyecto
* `api/` con `index.php`, `routes.php`, `config/` y `src/` (Controllers, Models y Utils).
* `front/index.html` (landing).
* Carpeta `front/dist/css` para los estilos.
* Carpeta `front/dist/js` con `api.js`, `script.js` y `pages/` (login, admin y user).
* Carpeta `front/dist/paginas` con login, registro, `admin/` y `user/`.
* Carpeta `docs/` con el MER, pasaje a tablas y glosario.

Alumna: Belén Rodríguez
