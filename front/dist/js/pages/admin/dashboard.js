let articulos = [];
let idParaEliminar = null;
let modalArticulo = null;
let modalEliminar = null;

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
    document.getElementById('panel').classList.remove('d-none');

    document.getElementById('nombre-usuario').textContent = sesion.data.nombre || sesion.data.email;
    document.getElementById('saludo-nombre').textContent = sesion.data.nombre;

    modalArticulo = new bootstrap.Modal(document.getElementById('modalArticulo'));
    modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));

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

    document.getElementById('btn-agregar').addEventListener('click', abrirAgregar);
    document.getElementById('form-articulo').addEventListener('submit', guardarArticulo);
    document.getElementById('btn-confirmar-eliminar').addEventListener('click', confirmarEliminar);

    cargarResumen();
    cargarArticulos();
    cargarUsuarios();
    cargarOpciones();
});

async function cargarResumen() {
    const respuesta = await API.request('/admin/resumen');
    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message);
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
        tabla.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No se pudieron cargar los artículos</td></tr>';
        mostrarMensaje('danger', respuesta.message);
        return;
    }

    articulos = respuesta.data;

    if (articulos.length === 0) {
        tabla.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Todavía no hay artículos cargados</td></tr>';
        return;
    }

    tabla.innerHTML = articulos.map(a => {
        const imagen = a.imagen
            ? `<img src="../../assets/${a.imagen}" alt="${a.titulo}" class="miniatura-articulo shadow-sm">`
            : '<div class="miniatura-articulo bg-light border"></div>';
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
            <td class="d-none d-xl-table-cell">${a.nombre_editorial || '-'}</td>
            <td class="d-none d-md-table-cell">${a.anio_publicacion || '-'}</td>
            <td>${estado}</td>
            <td class="text-end text-nowrap">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirEditar(${a.id_articulo})">Editar</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="abrirEliminar(${a.id_articulo})">Eliminar</button>
            </td>
        </tr>`;
    }).join('');
}

async function cargarUsuarios() {
    const tabla = document.getElementById('tabla-usuarios');
    const respuesta = await API.request('/admin/usuarios');

    if (respuesta.status !== 'ok') {
        tabla.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No se pudieron cargar los usuarios</td></tr>';
        return;
    }

    tabla.innerHTML = respuesta.data.map(u => {
        const rol = u.nombre_rol === 'Administrador'
            ? '<span class="badge text-bg-dark">Administrador</span>'
            : '<span class="badge text-bg-primary">Usuario</span>';

        return `
        <tr>
            <td class="text-muted">${u.id_usuario}</td>
            <td class="fw-medium">${u.nombre}</td>
            <td class="d-none d-md-table-cell">${u.email}</td>
            <td>${rol}</td>
            <td class="d-none d-lg-table-cell">${u.nombre_categoria || '-'}</td>
            <td class="d-none d-lg-table-cell">${formatearFecha(u.fecha_registro)}</td>
            <td>${u.prestamos_activos}</td>
        </tr>`;
    }).join('');
}

async function cargarOpciones() {
    const respuesta = await API.request('/admin/opciones');
    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message);
        return;
    }

    const selectCategoria = document.getElementById('id_categoria');
    selectCategoria.innerHTML = '<option value="">Elija una categoría</option>';
    respuesta.data.categorias.forEach(categoria => {
        const opcion = document.createElement('option');
        opcion.value = categoria.id_categoria;
        opcion.textContent = categoria.nombre_categoria;
        selectCategoria.appendChild(opcion);
    });

    const selectEditorial = document.getElementById('id_editorial');
    selectEditorial.innerHTML = '<option value="">Sin editorial</option>';
    respuesta.data.editoriales.forEach(editorial => {
        const opcion = document.createElement('option');
        opcion.value = editorial.id_editorial;
        opcion.textContent = editorial.nombre_editorial;
        selectEditorial.appendChild(opcion);
    });

    const listaAutores = document.getElementById('lista-autores');
    listaAutores.innerHTML = '';
    respuesta.data.autores.forEach(autor => {
        const opcion = document.createElement('option');
        opcion.value = autor.nombre_autor;
        listaAutores.appendChild(opcion);
    });
}

function abrirAgregar() {
    const form = document.getElementById('form-articulo');
    form.reset();
    document.getElementById('id_articulo').value = '';
    document.getElementById('disponible').checked = true;
    document.getElementById('tituloModalArticulo').textContent = 'Agregar artículo';
    document.getElementById('ayuda-imagen').classList.add('d-none');
    ocultarErrorModal();
    modalArticulo.show();
}

async function abrirEditar(id) {
    const respuesta = await API.request('/admin/articulos/' + id);
    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message);
        return;
    }

    const a = respuesta.data;
    document.getElementById('form-articulo').reset();
    document.getElementById('id_articulo').value = a.id_articulo;
    document.getElementById('titulo').value = a.titulo;
    document.getElementById('id_categoria').value = a.id_categoria;
    document.getElementById('autor').value = a.nombre_autor;
    document.getElementById('id_editorial').value = a.id_editorial || '';
    document.getElementById('anio_publicacion').value = a.anio_publicacion || '';
    document.getElementById('descripcion').value = a.descripcion || '';
    document.getElementById('disponible').checked = Number(a.disponible) === 1;
    document.getElementById('tituloModalArticulo').textContent = 'Editar artículo';
    document.getElementById('ayuda-imagen').classList.remove('d-none');
    ocultarErrorModal();
    modalArticulo.show();
}

async function guardarArticulo(evento) {
    evento.preventDefault();
    ocultarErrorModal();

    const id = document.getElementById('id_articulo').value;
    const titulo = document.getElementById('titulo').value.trim();
    const autor = document.getElementById('autor').value.trim();
    const idCategoria = document.getElementById('id_categoria').value;
    const archivo = document.getElementById('imagen').files[0];

    if (titulo === '') { mostrarErrorModal('Escriba el título'); return; }
    if (idCategoria === '') { mostrarErrorModal('Elija una categoría'); return; }
    if (autor === '') { mostrarErrorModal('Escriba el autor'); return; }
    if (archivo && archivo.size > 2 * 1024 * 1024) { mostrarErrorModal('La imagen no puede pesar más de 2 MB'); return; }

    const datos = {
        titulo: titulo,
        descripcion: document.getElementById('descripcion').value.trim(),
        autor: autor,
        id_categoria: Number(idCategoria),
        id_editorial: Number(document.getElementById('id_editorial').value),
        anio_publicacion: Number(document.getElementById('anio_publicacion').value),
        disponible: document.getElementById('disponible').checked,
        imagen: archivo ? await leerImagen(archivo) : ''
    };

    const botonGuardar = document.getElementById('btn-guardar');
    botonGuardar.disabled = true;

    const respuesta = id
        ? await API.request('/admin/articulos/' + id, 'PUT', datos)
        : await API.request('/admin/articulos', 'POST', datos);

    botonGuardar.disabled = false;

    if (respuesta.status !== 'ok') {
        mostrarErrorModal(respuesta.message || 'No se pudo guardar el artículo');
        return;
    }

    modalArticulo.hide();
    mostrarMensaje('success', respuesta.message);
    cargarArticulos();
    cargarResumen();
    cargarOpciones();
}

function abrirEliminar(id) {
    const articulo = articulos.find(a => a.id_articulo === id);
    idParaEliminar = id;
    document.getElementById('nombre-eliminar').textContent = articulo ? articulo.titulo : 'este artículo';
    modalEliminar.show();
}

async function confirmarEliminar() {
    const respuesta = await API.request('/admin/articulos/' + idParaEliminar, 'DELETE');
    modalEliminar.hide();

    if (respuesta.status !== 'ok') {
        mostrarMensaje('danger', respuesta.message || 'No se pudo eliminar el artículo');
        return;
    }

    mostrarMensaje('success', respuesta.message);
    cargarArticulos();
    cargarResumen();
    cargarUsuarios();
}

function leerImagen(archivo) {
    return new Promise(resolve => {
        const lector = new FileReader();
        lector.onload = () => resolve(lector.result);
        lector.readAsDataURL(archivo);
    });
}

function formatearFecha(fecha) {
    if (!fecha) {
        return '-';
    }
    const partes = fecha.substring(0, 10).split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function mostrarMensaje(tipo, mensaje) {
    const alerta = document.getElementById('alerta-admin');
    alerta.classList.remove('d-none', 'alert-success', 'alert-danger');
    alerta.classList.add('alert-' + tipo);
    alerta.textContent = mensaje || 'Error al comunicarse con la API';
}

function mostrarErrorModal(mensaje) {
    const alerta = document.getElementById('alerta-articulo');
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');
}

function ocultarErrorModal() {
    const alerta = document.getElementById('alerta-articulo');
    alerta.textContent = '';
    alerta.classList.add('d-none');
}
