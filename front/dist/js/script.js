// Productos (vienen de la base de datos)
let productos = [];
let sesionActual = null;

const contenedor = document.getElementById("contenedor-catalogo");

// Color principal
let colorActualInterface = "#2b6cb0"; 

// Pintar tarjetas en el HTML
function mostrarProductos(listaAFiltrar) {
    const tarjetasHtml = listaAFiltrar.map(p => {
        return `
        <div class="col elemento-catalogo">
            <div class="card h-100 border-0 shadow-sm card-hover">
                <div class="card-img-top bg-light text-center py-4 position-relative" style="height: 220px;">
                    <span class="badge position-absolute" style="top: 10px; left: 10px; z-index: 10; background-color: ${colorActualInterface}; text-white;">${p.badge}</span>
                    ${p.imagen
                        ? `<img src="${p.imagen}" alt="${p.titulo}" class="shadow rounded mx-auto d-block" style="width: 110px; height: 160px; object-fit: cover;">`
                        : `<div class="shadow rounded mx-auto d-flex align-items-center justify-content-center bg-white text-muted small" style="width: 110px; height: 160px;">Sin imagen</div>`}
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold text-dark mb-1">${p.titulo}</h5>
                    <p class="text-muted small mb-3">${p.autor}</p>
                    <p class="card-text text-secondary small flex-grow-1">${p.descripcion}</p>
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                        ${p.disponible
                            ? '<span class="text-success small fw-medium">disponible</span>'
                            : '<span class="text-secondary small fw-medium">no disponible</span>'}
                        <button class="btn btn-sm px-3" onclick="pedirArticulo(${p.id})" ${p.disponible ? '' : 'disabled'} style="color: ${colorActualInterface}; border: 1px solid ${colorActualInterface}; background-color: transparent;">${p.accion}</button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    contenedor.innerHTML = tarjetasHtml;
}

// Cargar catálogo desde la API
async function cargarCatalogo() {
    const respuesta = await API.request('/articulos');

    if (respuesta.status !== 'ok') {
        contenedor.innerHTML = '<p class="text-center text-muted w-100">No se pudo cargar el catálogo</p>';
        return;
    }

    productos = respuesta.data.map(a => {
        return {
            id: a.id_articulo,
            titulo: a.titulo,
            autor: a.nombre_autor,
            categoria: a.nombre_categoria.toLowerCase() + 's',
            badge: a.nombre_categoria,
            imagen: a.imagen ? 'dist/assets/' + a.imagen : null,
            descripcion: a.descripcion || 'Contenido disponible en formato digital para tu biblioteca personal.',
            disponible: Number(a.disponible) === 1,
            accion: a.accion
        };
    });

    mostrarProductos(productos);
}

// Menú según la sesión
async function cargarSesion() {
    const sesion = await API.request('/sesion');
    if (sesion.status !== 'ok') {
        return;
    }

    sesionActual = sesion.data;

    document.querySelectorAll('.menu-invitado').forEach(item => item.classList.add('d-none'));
    document.querySelectorAll('.menu-sesion').forEach(item => item.classList.remove('d-none'));

    document.getElementById('nombre-sesion').textContent = 'Hola, ' + sesionActual.nombre;
    document.getElementById('btn-panel').href = sesionActual.rol === 'Administrador'
        ? 'dist/paginas/admin/dashboard.html'
        : 'dist/paginas/user/inicio.html';

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.reload();
    });
}

// Pedir préstamo desde el catálogo
async function pedirArticulo(id) {
    if (!sesionActual) {
        window.location.href = 'dist/paginas/login.html';
        return;
    }

    const respuesta = await API.request('/usuario/prestamos', 'POST', { id_articulo: id });
    alert(respuesta.message || 'No se pudo pedir el artículo');
}

if (contenedor) {
    cargarCatalogo();
    cargarSesion();
}


// Filtrado y colores de la interfaz
function filtrar(categoriaSeleccionada) {
    
    // Switch para elegir el color según categoría
    switch (categoriaSeleccionada) {
        case "todos":
            colorActualInterface = "#2b6cb0"; 
            break;
        case "libros":
            colorActualInterface = "#e53e3e";
            break;
        case "revistas":
            colorActualInterface = "#319795"; 
            break;
        case "audiolibros":
            colorActualInterface = "#d69e2e"; 
            break;
        default:
            colorActualInterface = "#2b6cb0";
            break;
    }

    // Capturar elementos HTML
    const marcaNav = document.getElementById("marca-nav");
    const btnIngresar = document.getElementById("btn-ingresar");
    const btnRegistrarse = document.getElementById("btn-registrarse");
    const btnExplorar = document.getElementById("btn-explorar");
    const btnPanel = document.getElementById("btn-panel");

    // Cambiar color del logo
    marcaNav.style.setProperty('color', colorActualInterface, 'important');

    // Cambiar botón ingresar
    btnIngresar.style.setProperty('color', colorActualInterface, 'important');
    btnIngresar.style.setProperty('border-color', colorActualInterface, 'important');

    // Cambiar botón registrarse
    btnRegistrarse.style.setProperty('background-color', colorActualInterface, 'important');

    // Cambiar botón del panel
    if (btnPanel) {
        btnPanel.style.setProperty('background-color', colorActualInterface, 'important');
    }

    // Cambiar botón explorar
    if(btnExplorar) {
        btnExplorar.style.setProperty('background-color', colorActualInterface, 'important');
    }

    // Guardar elementos en la lista según el filtro
    const resultadoFiltrado = [];
    productos.forEach(p => {
        if (categoriaSeleccionada === "todos") {
            resultadoFiltrado.push(p);
        } else if (p.categoria === categoriaSeleccionada) {
            resultadoFiltrado.push(p);
        }
    });

    // Redibuja las tarjetas aplicando el filtro y el nuevo color
    mostrarProductos(resultadoFiltrado);
}

// Validación de Registro
function validarRegistro() {
    const nombreInput = document.getElementById("regNombre");
    const emailInput = document.getElementById("regEmail");
    const passInput = document.getElementById("regPassword");
    const passValue = passInput.value; 
    if (nombreInput.value === "") { alert("Ingrese nombre"); return false; }
    if (emailInput.value === "") { alert("Ingrese correo"); return false; }
    if (!emailInput.value.includes("@") || !emailInput.value.includes(".com")) { alert("El correo debe incluir @ y .com"); return false; }
    if (passValue === "") { alert("Introduzca contraseña"); return false; }
    if (passValue.length < 6) { alert("La contraseña debe tener al menos 6 caracteres"); return false; }
    alert("registro exitoso");
    return true;
}