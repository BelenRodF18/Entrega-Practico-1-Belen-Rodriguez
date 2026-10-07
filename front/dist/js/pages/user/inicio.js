let prestamosActivos = [];

document.addEventListener('DOMContentLoaded', async () => {
    const sesion = await API.request('/sesion');

    if (sesion.status !== 'ok') {
        window.location.href = '../login.html';
        return;
    }

    if (sesion.data.rol === 'Administrador') {
        window.location.href = '../admin/dashboard.html';
        return;
    }

    document.getElementById('verificando').classList.add('d-none');
    document.getElementById('panel').classList.remove('d-none');

    document.getElementById('nombre-usuario').textContent = sesion.data.nombre || sesion.data.email;
    document.getElementById('saludo-nombre').textContent = sesion.data.nombre;

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.href = '../login.html';
    });

    document.querySelectorAll('#menuPanel a.nav-link').forEach(enlace => {
        enlace.addEventListener('click', () => {
            document.querySelectorAll('#menuPanel a.nav-link').forEach(otro => otro.classList.remove('active'));
            enlace.classList.add('active');

            const menu = bootstrap.Offcanvas.getInstance(document.getElementById('menuPanel'));
            if (menu) {
                menu.hide();
            }
        });
    });

    cargarPerfil();
    await cargarPrestamos();
    cargarCatalogo();
});

async function cargarPerfil() {
    const respuesta = await API.request('/usuario/perfil');
    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message);
        return;
    }

    document.getElementById('perfil-email').textContent = respuesta.data.email;
    document.getElementById('perfil-interes').textContent = respuesta.data.nombre_categoria || 'Sin elegir';
}

async function cargarPrestamos() {
    const tabla = document.getElementById('tabla-prestamos');
    const respuesta = await API.request('/usuario/prestamos');

    if (respuesta.status !== 'ok') {
        tabla.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No se pudieron cargar los préstamos</td></tr>';
        return;
    }

    const prestamos = respuesta.data;
    prestamosActivos = prestamos.filter(p => p.estado === 'Activo').map(p => p.id_articulo);

    document.getElementById('total-activos').textContent = prestamosActivos.length;
    document.getElementById('total-prestamos').textContent = prestamos.length;

    if (prestamos.length === 0) {
        tabla.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Todavía no pidió ningún artículo</td></tr>';
        return;
    }

    tabla.innerHTML = prestamos.map(p => {
        const imagen = p.imagen
            ? `<img src="../../assets/${p.imagen}" alt="${p.titulo}" class="miniatura-articulo shadow-sm">`
            : '<div class="miniatura-articulo bg-light border"></div>';
        const activo = p.estado === 'Activo';

        return `
        <tr>
            <td>
                <div class="d-flex align-items-center gap-3">
                    ${imagen}
                    <div>
                        <span class="fw-medium d-block">${p.titulo}</span>
                        <span class="text-muted small">${p.nombre_autor}</span>
                    </div>
                </div>
            </td>
            <td class="d-none d-md-table-cell">${p.nombre_categoria}</td>
            <td class="d-none d-md-table-cell">${formatearFecha(p.fecha_prestamo)}</td>
            <td class="d-none d-lg-table-cell">${formatearFecha(p.fecha_devolucion)}</td>
            <td>${activo ? '<span class="badge text-bg-success">Activo</span>' : '<span class="badge text-bg-secondary">Devuelto</span>'}</td>
            <td class="text-end">
                ${activo ? `<button type="button" class="btn btn-sm btn-outline-primary" onclick="devolver(${p.id_prestamo})">Devolver</button>` : ''}
            </td>
        </tr>`;
    }).join('');
}

async function cargarCatalogo() {
    const contenedor = document.getElementById('contenedor-catalogo');
    const respuesta = await API.request('/articulos');

    if (respuesta.status !== 'ok') {
        contenedor.innerHTML = '<p class="text-muted">No se pudo cargar el catálogo</p>';
        return;
    }

    const disponibles = respuesta.data.filter(a => Number(a.disponible) === 1);

    if (disponibles.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">No hay artículos disponibles por ahora</p>';
        return;
    }

    contenedor.innerHTML = disponibles.map(a => {
        const yaLoTiene = prestamosActivos.includes(a.id_articulo);
        const imagen = a.imagen
            ? `<img src="../../assets/${a.imagen}" alt="${a.titulo}" class="portada-catalogo rounded shadow-sm">`
            : '<div class="portada-catalogo rounded bg-light border"></div>';

        return `
        <div class="col">
            <div class="card h-100 border-0 shadow-sm card-hover">
                <div class="card-body d-flex gap-3">
                    ${imagen}
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="badge text-bg-light align-self-start mb-2">${a.nombre_categoria}</span>
                        <h3 class="h6 fw-bold mb-1">${a.titulo}</h3>
                        <p class="text-muted small mb-3">${a.nombre_autor}</p>
                        <button type="button" class="btn btn-sm btn-primary mt-auto align-self-start" onclick="pedir(${a.id_articulo})" ${yaLoTiene ? 'disabled' : ''}>
                            ${yaLoTiene ? 'En préstamo' : a.accion}
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

async function pedir(idArticulo) {
    const respuesta = await API.request('/usuario/prestamos', 'POST', { id_articulo: idArticulo });

    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message || 'No se pudo pedir el artículo');
        return;
    }

    mostrarMensaje('success', respuesta.message);
    await cargarPrestamos();
    cargarCatalogo();
}

async function devolver(idPrestamo) {
    const respuesta = await API.request('/usuario/prestamos/' + idPrestamo + '/devolver', 'POST');

    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message || 'No se pudo devolver el artículo');
        return;
    }

    mostrarMensaje('success', respuesta.message);
    await cargarPrestamos();
    cargarCatalogo();
}

function formatearFecha(fecha) {
    if (!fecha) {
        return '-';
    }
    const partes = fecha.substring(0, 10).split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function mostrarMensaje(tipo, mensaje) {
    const alerta = document.getElementById('alerta-usuario');
    alerta.classList.remove('d-none', 'alert-success', 'alert-danger');
    alerta.classList.add('alert-' + tipo);
    alerta.textContent = mensaje;
    alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
