document.addEventListener('DOMContentLoaded', async () => {
    const sesion = await API.request('/sesion');

    if (sesion.status !== 'ok') {
        window.location.href = '../login.html';
        return;
    }

    document.getElementById('nombre-usuario').textContent = sesion.data.nombre || sesion.data.email;

    document.getElementById('btn-logout').addEventListener('click', async () => {
        await API.request('/logout', 'POST');
        window.location.href = '../login.html';
    });
});
