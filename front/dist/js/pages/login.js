// Redirección según rol
function redirigirSegunRol(rol) {
    if (rol === 'Administrador') {
        window.location.href = 'admin/dashboard.html';
    } else {
        window.location.href = 'user/inicio.html';
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const sesion = await API.request('/sesion');
    if (sesion.status === 'ok') {
        redirigirSegunRol(sesion.data.rol);
        return;
    }

    const form = document.getElementById('formLogin');
    const alerta = document.getElementById('alerta-login');

    function mostrarError(mensaje) {
        alerta.textContent = mensaje;
        alerta.classList.remove('d-none');
    }

    form.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        alerta.classList.add('d-none');
        alerta.textContent = '';

        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;

        if (email === '') { mostrarError('Escriba el correo'); return; }
        if (!email.includes('@') || !email.includes('.com')) { mostrarError('Formato correo invalido'); return; }
        if (password === '') { mostrarError('Introduzca contraseña'); return; }

        const respuesta = await API.request('/login', 'POST', {
            email: email,
            password: password
        });

        if (respuesta.status === 'ok') {
            redirigirSegunRol(respuesta.data.rol);
            return;
        }

        mostrarError(respuesta.message || 'Correo o contraseña incorrectos');
    });
});
