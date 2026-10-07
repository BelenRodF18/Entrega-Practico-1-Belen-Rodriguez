//Array
const productos = [
    {
        id: 1,
        titulo: "Las aventuras de Pinocho",
        autor: "Carlo Collodi",
        categoria: "libros",
        badge: "Libro",
        imagen: "dist/assets/libro1.jpg", 
        accion: "Leer"
    },
    {
        id: 2,
        titulo: "La chica invisible",
        autor: "Blue Jeans",
        categoria: "audiolibros",
        badge: "Audiolibro",
        imagen: "dist/assets/audiolibro1.jpg",
        accion: "Escuchar"
    },
    {
        id: 3,
        titulo: "Revista Vogue ",
        autor: "Maria Gonzales",
        categoria: "revistas",
        badge: "Revista",
        imagen: "dist/assets/revista1.jpg",
        accion: "Leer"
    }
];

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
                    <img src="${p.imagen}" alt="${p.titulo}" class="shadow rounded mx-auto d-block" style="width: 110px; height: 160px; object-fit: cover;">
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold text-dark mb-1">${p.titulo}</h5>
                    <p class="text-muted small mb-3">${p.autor}</p>
                    <p class="card-text text-secondary small flex-grow-1">Contenido disponible en formato digital para tu biblioteca personal.</p>
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                        <span class="text-success small fw-medium">disponible</span>
                        <button class="btn btn-sm px-3" style="color: ${colorActualInterface}; border: 1px solid ${colorActualInterface}; background-color: transparent;">${p.accion}</button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    contenedor.innerHTML = tarjetasHtml;
}

if (contenedor) {
    mostrarProductos(productos);
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

    // Cambiar color del logo
    marcaNav.style.setProperty('color', colorActualInterface, 'important');

    // Cambiar botón ingresar
    btnIngresar.style.setProperty('color', colorActualInterface, 'important');
    btnIngresar.style.setProperty('border-color', colorActualInterface, 'important');

    // Cambiar botón registrarse
    btnRegistrarse.style.setProperty('background-color', colorActualInterface, 'important');

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