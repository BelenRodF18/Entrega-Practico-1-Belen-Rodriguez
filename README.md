# Práctico 2: Base de Datos, Login y Panel de Administración (Biblioteca Virtual)

Continuación del Práctico 1. Sobre la misma Landing Page de BiblioDigital se agregó la base de datos, una API en PHP con MVC y un login que diferencia entre Administrador y Usuario.

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
* Estado de la API: http://localhost:8080/api/estado
* phpMyAdmin: http://localhost:8081 (root / root_password)

Si se modifica el `init.sql` hay que borrar el volumen con `docker compose down -v` y volver a levantar.

##  Usuarios de prueba
* **Administrador:** admin@bibliodigital.com / admin123
* **Usuario:** usuario@bibliodigital.com / usuario123

##  Características principales
* **Login:** el formulario manda el correo y la contraseña a `/api/login`. La contraseña se compara con el hash guardado en la base.
* **Roles:** al iniciar sesión se guarda el rol del usuario en la sesión de PHP.
* **Redirección según el rol:** el Administrador va a `dist/paginas/admin/dashboard.html` y el Usuario a `dist/paginas/user/inicio.html`.
* **Dashboard de Administración:** muestra el usuario conectado, opción de cerrar sesión, menú del administrador, un resumen y la sección de artículos de la biblioteca.
* **Protección:** si no hay sesión de administrador el Dashboard redirige, y las rutas `/api/admin/...` responden 401 o 403.

##  Estructura del Proyecto
* `api/` con `index.php`, `routes.php`, `config/` y `src/` (Controllers, Models y Utils).
* `front/index.html` (landing).
* Carpeta `front/dist/css` para los estilos.
* Carpeta `front/dist/js` con `api.js`, `script.js` y `pages/` (login, admin y user).
* Carpeta `front/dist/paginas` con login, registro, `admin/` y `user/`.
* Carpeta `docs/` con el MER, pasaje a tablas y glosario.

Alumna: Belén Rodríguez
