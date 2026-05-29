// CSRF TOKEN
const TOKEN_CSRF = document.querySelector('meta[name="csrf-token"]').content;

// TEMA
function alternarTemaAdmin() {
    const body = document.body;
    const solAdmin = document.getElementById('icono-sol-admin');
    const lunaAdmin = document.getElementById('icono-luna-admin');

    if (body.classList.contains('light')) {
        body.classList.remove('light');
        solAdmin.style.display = 'none';
        lunaAdmin.style.display = 'block';
        localStorage.setItem('tema', 'dark');
    } else {
        body.classList.add('light');
        solAdmin.style.display = 'block';
        lunaAdmin.style.display = 'none';
        localStorage.setItem('tema', 'light');
    }
}

// Restaurar tema guardado
(function () {
    const temaGuardado = localStorage.getItem('tema');
    if (temaGuardado === 'dark') {
        document.body.classList.remove('light');
        const solAdmin = document.getElementById('icono-sol-admin');
        const lunaAdmin = document.getElementById('icono-luna-admin');
        if (solAdmin && lunaAdmin) {
            solAdmin.style.display = 'none';
            lunaAdmin.style.display = 'block';
        }
    }
})();

// TOAST
function mostrarToast(mensaje, tipo = 'exito') {
    // Eliminar toast anterior si existe
    const anterior = document.querySelector('.admin-toast');
    if (anterior) anterior.remove();

    const toast = document.createElement('div');
    toast.className = `admin-toast ${tipo}`;
    toast.innerHTML = `
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            ${tipo === 'exito'
            ? '<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'
            : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}
        </svg>
        ${mensaje}
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('saliendo');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// TOGGLE MODELO CHECKBOX
function toggleModelo(label) {
    const checkbox = label.querySelector('input[type="checkbox"]');
    if (checkbox.disabled) return;

    //El click del label ya togglea el checkbox, solo actualizar clase
    setTimeout(() => {
        if (checkbox.checked) {
            label.classList.add('checked');
        } else {
            label.classList.remove('checked');
        }
    }, 0);
}

// GUARDAR MODELOS
async function guardarModelos(userId) {
    const fila = document.querySelector(`tr[data-user-id="${userId}"]`);
    const checkboxes = fila.querySelectorAll('.modelo-check input[type="checkbox"]');
    const modelos = [];

    checkboxes.forEach(cb => {
        if (cb.checked) {
            modelos.push(cb.closest('.modelo-check').dataset.modelo);
        }
    });

    if (modelos.length === 0) {
        mostrarToast('Debes seleccionar al menos un modelo', 'error');
        return;
    }

    try {
        const res = await fetch(`/admin/usuarios/${userId}/modelos`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ modelos }),
        });

        const data = await res.json();

        if (data.ok) {
            mostrarToast('Modelos actualizados correctamente');
        } else {
            mostrarToast(data.message || 'Error al actualizar', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// EDITAR USUARIO
let editarUserId = null;

function abrirEditar(userId, nombre, email) {
    editarUserId = userId;
    document.getElementById('editar-nombre').value = nombre;
    document.getElementById('editar-email').value = email;
    document.getElementById('editar-password').value = '';
    document.getElementById('modal-editar-usuario').style.display = 'flex';
}

function cerrarModalEditar() {
    document.getElementById('modal-editar-usuario').style.display = 'none';
    editarUserId = null;
}

async function ejecutarEditar() {
    const nombre = document.getElementById('editar-nombre').value.trim();
    const email = document.getElementById('editar-email').value.trim();
    const password = document.getElementById('editar-password').value;

    if (!nombre || !email) {
        mostrarToast('Nombre y email son obligatorios', 'error');
        return;
    }

    if (password && password.length < 8) {
        mostrarToast('La contraseña debe tener al menos 8 caracteres', 'error');
        return;
    }
    // Construir el body con los datos del usuario
    const body = { name: nombre, email };
    // Si se introduce una contraseña, añadirla al body
    if (password) body.password = password;

    // Realizar la petición PATCH para actualizar el usuario
    try {
        const res = await fetch(`/admin/usuarios/${editarUserId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();

        if (data.ok) {
            mostrarToast(password ? 'Usuario y contraseña actualizados' : 'Usuario actualizado correctamente');
            const fila = document.querySelector(`tr[data-user-id="${editarUserId}"]`);
            fila.querySelector('.nombre-usuario-tabla').childNodes[0].textContent = nombre + ' ';
            fila.querySelector('.email-usuario-tabla').textContent = email;
            fila.querySelector('.avatar-tabla').textContent = nombre.charAt(0).toUpperCase();
            fila.dataset.userName = nombre;
            fila.dataset.userEmail = email;
            cerrarModalEditar();

            // Actualizar filtros de paginación
            const query = document.getElementById('buscar-usuario').value.toLowerCase().trim();
            const filas = Array.from(document.querySelectorAll('.fila-usuario'));
            if (query === '') {
                filasFiltradas = filas;
            } else {
                filasFiltradas = filas.filter(f => {
                    const nombreUser = (f.dataset.userName || '').toLowerCase();
                    const emailUser = (f.dataset.userEmail || '').toLowerCase();
                    return nombreUser.includes(query) || emailUser.includes(query);
                });
            }
            irAPagina(paginaActual);
        } else {
            mostrarToast(data.message || 'Error al actualizar', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// CREAR USUARIO
function abrirCrear() {
    document.getElementById('crear-nombre').value = '';
    document.getElementById('crear-email').value = '';
    document.getElementById('crear-password').value = '';
    document.getElementById('modal-crear-usuario').style.display = 'flex';
}

function cerrarModalCrear() {
    document.getElementById('modal-crear-usuario').style.display = 'none';
}

async function ejecutarCrear() {
    const nombre = document.getElementById('crear-nombre').value.trim();
    const email = document.getElementById('crear-email').value.trim();
    const password = document.getElementById('crear-password').value;

    if (!nombre || !email || !password) {
        mostrarToast('Todos los campos son obligatorios', 'error');
        return;
    }

    if (password.length < 8) {
        mostrarToast('La contraseña debe tener al menos 8 caracteres', 'error');
        return;
    }

    try {
        const res = await fetch('/admin/usuarios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: nombre, email, password }),
        });

        const data = await res.json();

        if (data.ok) {
            mostrarToast('Usuario creado correctamente');
            cerrarModalCrear();
            // Recargar para mostrar nuevo usuario
            setTimeout(() => location.reload(), 500);
        } else {
            // Mostrar errores de validación
            if (data.errors) {
                const msgs = Object.values(data.errors).flat();
                mostrarToast(msgs[0] || 'Error de validación', 'error');
            } else {
                mostrarToast(data.message || 'Error al crear usuario', 'error');
            }
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// TOGGLE ACTIVO
async function toggleActivo(userId) {
    try {
        const res = await fetch(`/admin/usuarios/${userId}/toggle-activo`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN_CSRF,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.ok) {
            mostrarToast(data.message);
            // Recargar para actualizar estado visual
            setTimeout(() => location.reload(), 500);
        } else {
            mostrarToast(data.message || 'Error', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// ELIMINAR USUARIO
let eliminarUserId = null;

function confirmarEliminar(userId, nombre) {
    eliminarUserId = userId;
    document.getElementById('nombre-eliminar').textContent = nombre;
    document.getElementById('modal-eliminar-usuario').style.display = 'flex';
}

function cerrarModalEliminar() {
    document.getElementById('modal-eliminar-usuario').style.display = 'none';
    eliminarUserId = null;
}

async function ejecutarEliminar() {
    if (!eliminarUserId) return;

    try {
        const res = await fetch(`/admin/usuarios/${eliminarUserId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': TOKEN_CSRF,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.ok) {
            mostrarToast('Usuario eliminado correctamente');
            // Eliminar fila de la tabla
            const fila = document.querySelector(`tr[data-user-id="${eliminarUserId}"]`);
            if (fila) {
                fila.style.transition = 'opacity 0.3s, transform 0.3s';
                fila.style.opacity = '0';
                fila.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    fila.remove();
                    // Recalcular paginación
                    const query = document.getElementById('buscar-usuario').value.toLowerCase().trim();
                    const filas = Array.from(document.querySelectorAll('.fila-usuario'));
                    if (query === '') {
                        filasFiltradas = filas;
                    } else {
                        filasFiltradas = filas.filter(f => {
                            const nombre = (f.dataset.userName || '').toLowerCase();
                            const email = (f.dataset.userEmail || '').toLowerCase();
                            return nombre.includes(query) || email.includes(query);
                        });
                    }
                    const totalPag = Math.ceil(filasFiltradas.length / itemsPorPagina);
                    if (paginaActual > totalPag) {
                        irAPagina(totalPag);
                    } else {
                        irAPagina(paginaActual);
                    }
                }, 300);
            }
            cerrarModalEliminar();
        } else {
            mostrarToast(data.message || 'Error al eliminar', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexión', 'error');
    }
}

// PAGINACIÓN
let paginaActual = 1;
const itemsPorPagina = 6;
let filasFiltradas = [];

function inicializarPaginacion() {
    const filas = Array.from(document.querySelectorAll('.fila-usuario'));
    filasFiltradas = filas;
    irAPagina(1);
}

function irAPagina(pagina) {
    const totalPaginas = Math.ceil(filasFiltradas.length / itemsPorPagina);
    if (pagina < 1) pagina = 1;
    if (pagina > totalPaginas && totalPaginas > 0) pagina = totalPaginas;
    paginaActual = pagina;

    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;

    // Ocultar todas las filas primero
    const todasFilas = document.querySelectorAll('.fila-usuario');
    todasFilas.forEach(f => f.style.display = 'none');

    // Mostrar solo las correspondientes a la página actual dentro de las filtradas
    filasFiltradas.forEach((fila, index) => {
        if (index >= inicio && index < fin) {
            fila.style.display = '';
        }
    });

    renderizarPaginacion(totalPaginas);
}

//RENDERIZAR PAGINACIÓN
function renderizarPaginacion(totalPaginas) {
    const container = document.getElementById('paginacion-container');
    if (!container) return;

    //Si solo hay una página no mostrar paginación
    if (totalPaginas <= 1) {
        container.innerHTML = '';
        if (totalPaginas === 1) {
            filasFiltradas.forEach(f => f.style.display = '');
        }
        return;
    }

    //Número de página a mostrar
    const inicioMuestra = (paginaActual - 1) * itemsPorPagina + 1;
    const finMuestra = Math.min(inicioMuestra + itemsPorPagina - 1, filasFiltradas.length);
    const totalRegistros = filasFiltradas.length;

    //HTML de paginación
    let html = `
        <div class="paginacion-info">
            Mostrando <strong>${inicioMuestra}-${finMuestra}</strong> de <strong>${totalRegistros}</strong> usuarios
        </div>
        <div class="paginacion-controles">
            <button class="paginacion-btn" ${paginaActual === 1 ? 'disabled' : ''} onclick="irAPagina(${paginaActual - 1})">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </button>
    `;

    //Número de páginas
    for (let i = 1; i <= totalPaginas; i++) {
        html += `
            <button class="paginacion-btn num ${i === paginaActual ? 'activo' : ''}" onclick="irAPagina(${i})">${i}</button>
        `;
    }

    //Siguiente página
    html += `
            <button class="paginacion-btn" ${paginaActual === totalPaginas ? 'disabled' : ''} onclick="irAPagina(${paginaActual + 1})">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </button>
        </div>
    `;

    container.innerHTML = html;
}

// FILTRAR USUARIOS
function filtrarUsuarios(query) {
    const filas = Array.from(document.querySelectorAll('.fila-usuario'));
    const q = query.toLowerCase().trim();

    if (q === '') {
        filasFiltradas = filas;
    } else {
        filasFiltradas = filas.filter(fila => {
            const nombre = (fila.dataset.userName || '').toLowerCase();
            const email = (fila.dataset.userEmail || '').toLowerCase();
            return nombre.includes(q) || email.includes(q);
        });
    }

    irAPagina(1);
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', () => {
    inicializarPaginacion();
});


// Exponer funciones en el objeto global window para su uso en los atributos de eventos HTML
window.alternarTemaAdmin = alternarTemaAdmin;
window.mostrarToast = mostrarToast;
window.toggleModelo = toggleModelo;
window.guardarModelos = guardarModelos;
window.abrirEditar = abrirEditar;
window.cerrarModalEditar = cerrarModalEditar;
window.ejecutarEditar = ejecutarEditar;
window.abrirCrear = abrirCrear;
window.cerrarModalCrear = cerrarModalCrear;
window.ejecutarCrear = ejecutarCrear;
window.toggleActivo = toggleActivo;
window.confirmarEliminar = confirmarEliminar;
window.cerrarModalEliminar = cerrarModalEliminar;
window.ejecutarEliminar = ejecutarEliminar;
window.irAPagina = irAPagina;
window.filtrarUsuarios = filtrarUsuarios;
