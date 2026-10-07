document.addEventListener('DOMContentLoaded', async () => {
    const sesion = await API.request('/sesion');

    if (sesion.status !== 'ok') {
        window.location.href = '../login.html';
        return;
    }

    if (sesion.data.rol !== 'Administrador') {
        window.location.href = '../user/inicio.html';
        return;
    }

    document.getElementById('verificando').classList.add('d-none');
    document.getElementById('panel-admin').classList.remove('d-none');

    document.getElementById('nombre-usuario').textContent = sesion.data.nombre || sesion.data.email;
    document.getElementById('saludo-nombre').textContent = sesion.data.nombre;

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.href = '../login.html';
    });

    document.querySelectorAll('#menuAdmin a.nav-link').forEach(enlace => {
        enlace.addEventListener('click', () => {
            const menu = bootstrap.Offcanvas.getInstance(document.getElementById('menuAdmin'));
            if (menu) {
                menu.hide();
            }
        });
    });

    cargarResumen();
    cargarArticulos();
});

async function cargarResumen() {
    const respuesta = await API.request('/admin/resumen');
    if (respuesta.status !== 'ok') {
        mostrarErrorAdmin(respuesta.message);
        return;
    }

    document.getElementById('total-articulos').textContent = respuesta.data.articulos;
    document.getElementById('total-disponibles').textContent = respuesta.data.disponibles;
    document.getElementById('total-usuarios').textContent = respuesta.data.usuarios;
    document.getElementById('total-admins').textContent = respuesta.data.administradores;
}

async function cargarArticulos() {
    const tabla = document.getElementById('tabla-articulos');
    const respuesta = await API.request('/admin/articulos');

    if (respuesta.status !== 'ok') {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No se pudieron cargar los artículos</td></tr>';
        mostrarErrorAdmin(respuesta.message);
        return;
    }

    if (respuesta.data.length === 0) {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Todavía no hay artículos cargados</td></tr>';
        return;
    }

    tabla.innerHTML = respuesta.data.map(a => {
        const imagen = a.imagen
            ? `<img src="../../assets/${a.imagen}" alt="${a.titulo}" class="miniatura-articulo shadow-sm">`
            : '<div class="miniatura-articulo bg-light"></div>';
        const estado = Number(a.disponible) === 1
            ? '<span class="badge text-bg-success">Disponible</span>'
            : '<span class="badge text-bg-secondary">No disponible</span>';

        return `
        <tr>
            <td class="text-muted">${a.id_articulo}</td>
            <td>
                <div class="d-flex align-items-center gap-3">
                    ${imagen}
                    <span class="fw-medium">${a.titulo}</span>
                </div>
            </td>
            <td>${a.nombre_categoria}</td>
            <td class="d-none d-md-table-cell">${a.nombre_autor}</td>
            <td class="d-none d-lg-table-cell">${a.nombre_editorial || '-'}</td>
            <td class="d-none d-md-table-cell">${a.anio_publicacion || '-'}</td>
            <td>${estado}</td>
        </tr>`;
    }).join('');
}

function mostrarErrorAdmin(mensaje) {
    const alerta = document.getElementById('alerta-admin');
    alerta.textContent = mensaje || 'Error al comunicarse con la API';
    alerta.classList.remove('d-none');
}
