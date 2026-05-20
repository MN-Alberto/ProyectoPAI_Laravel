document.addEventListener('DOMContentLoaded', function () {
    // Restaurar tema guardado
    const temaGuardado = localStorage.getItem('pai-theme');
    // Variable que almacena el icono del sol
    const sun = document.getElementById('icono-sol');
    // Variable que almacena el icono de la luna
    const moon = document.getElementById('icono-luna');

    if (temaGuardado === 'dark') {
        document.body.classList.remove('light');
        if (sun) sun.style.display = 'block';
        if (moon) moon.style.display = 'none';
    } else {
        document.body.classList.add('light');
        if (sun) sun.style.display = 'none';
        if (moon) moon.style.display = 'block';
    }

    // Desplaza la ventana hacia abajo al iniciar
    if (window.desplazarAbajo) {
        window.desplazarAbajo();
    }

    // Modal de perfil
    const avatar = document.querySelector('.avatar-usuario');
    const modal = document.getElementById('modal-perfil');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCancelar = document.getElementById('btn-cancelar-modal');

    // Cierra el modal y limpia los campos
    function cerrarYLimpiarModal() {
        if (modal) {
            // Oculta el modal
            modal.style.display = 'none';

            // Limpiar campos de contraseña
            const inputCurrent = document.getElementById('modal-current-password');
            const inputNew = document.getElementById('modal-password');
            const inputConfirm = document.getElementById('modal-password-confirm');

            if (inputCurrent) inputCurrent.value = '';
            if (inputNew) inputNew.value = '';
            if (inputConfirm) inputConfirm.value = '';

            const alertaError = modal.querySelector('.alerta-error');
            const alertaExito = modal.querySelector('.alerta-exito');

            if (alertaError) alertaError.remove();
            if (alertaExito) alertaExito.remove();
        }
    }

    // Si el avatar y el modal existen se agrega un evento de click al avatar
    if (avatar && modal) {
        avatar.addEventListener('click', function () {
            modal.style.display = 'flex';
        });
    }

    // Si el modal existe se agregan los eventos de cierre
    if (modal) {
        if (btnCerrar) {
            btnCerrar.addEventListener('click', cerrarYLimpiarModal);
        }
        if (btnCancelar) {
            btnCancelar.addEventListener('click', cerrarYLimpiarModal);
        }

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                cerrarYLimpiarModal();
            }
        });
    }
});
