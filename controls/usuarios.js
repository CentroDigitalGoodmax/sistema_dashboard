/* controls/usuarios.js
 * Gestión de usuarios — Argen Medical
 * Incluye: listar, crear, editar, cambiar contraseña, eliminar
 * Subida de avatar con FormData (multipart/form-data)
 */
(function ($) {
    'use strict';

    console.log('[usuarios.js] cargado correctamente');

    let usuariosData = [];

    /* ============================================================
     *  HELPERS
     * ============================================================ */
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(String(dateStr).replace(' ', 'T'));
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES') + ' ' + d.toLocaleTimeString('es-ES');
    }

    function avatarUrl(u) {
        if (u && u.avatar && String(u.avatar).trim() !== '') return u.avatar;
        const nombre = (u && u.nombre) ? u.nombre : 'U';
        return 'https://ui-avatars.com/api/?background=6366F1&color=fff&name=' +
            encodeURIComponent(nombre);
    }

    function openModal(id)  { $('#' + id).removeClass('hidden').addClass('flex'); }
    function closeModal(id) { $('#' + id).addClass('hidden').removeClass('flex'); }

    // Exponer funciones usadas en atributos onclick del HTML
    window.openModal         = openModal;
    window.closeModal        = closeModal;
    window.resetUsuarioModal = resetUsuarioModal;
    window.editUsuario       = editUsuario;
    window.deleteUsuario     = deleteUsuario;
    window.openPasswordModal = openPasswordModal;

    /* ============================================================
     *  LISTAR / RENDERIZAR
     * ============================================================ */
    function cargarUsuarios() {
        $.ajax({
            url: '/mi/models/consultarUsuarios',
            type: 'GET',
            dataType: 'json'
        })
        .done(function (response) {
            usuariosData = Array.isArray(response) ? response : [];
            renderizarUsuarios(usuariosData);
        })
        .fail(function () {
            $('#usuariosTableBody').html(
                '<tr><td colspan="6" class="text-center text-red-500 py-8">Error al cargar usuarios</td></tr>'
            );
        });
    }

    function renderizarUsuarios(usuarios) {
        const $tbody = $('#usuariosTableBody');

        if (!usuarios || usuarios.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-center text-gray-500 py-8">No hay usuarios disponibles</td></tr>');
            return;
        }

        let html = '';
        usuarios.forEach(function (u) {
            const estado = u.activo == 1
                ? '<span class="inline-block px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-800">Activo</span>'
                : '<span class="inline-block px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">Inactivo</span>';

            const ultimoAcceso = u.ultimo_acceso ? formatDate(u.ultimo_acceso) : 'Nunca';

            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="${escapeHtml(avatarUrl(u))}"
                             alt="${escapeHtml(u.nombre)}"
                             class="w-10 h-10 rounded-full object-cover border border-gray-200"
                             onerror="this.src='https://ui-avatars.com/api/?background=6366F1&color=fff&name=U'">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">${escapeHtml(u.nombre)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${escapeHtml(u.email)}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${estado}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${escapeHtml(ultimoAcceso)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                        <button onclick="editUsuario(${u.id})"
                                class="text-blue-600 hover:text-blue-800" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="openPasswordModal(${u.id})"
                                class="text-amber-600 hover:text-amber-800" title="Cambiar contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        <button onclick="deleteUsuario(${u.id})"
                                class="text-red-600 hover:text-red-800" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        $tbody.html(html);
    }

    /* ============================================================
     *  MODAL CREAR / EDITAR
     * ============================================================ */
    function resetUsuarioModal() {
        $('#usuarioModalTitle').text('Nuevo Usuario');
        $('#usuario_id').val('');
        $('#usuario_nombre').val('');
        $('#usuario_email').val('');
        $('#usuario_password').val('').prop('required', true);
        $('#usuario_password_confirm').val('');
        $('#usuario_avatar').val('');
        $('#usuario_avatar_file').val('');
        $('#usuario_activo').prop('checked', true);
        $('#passwordRequired').text('*');
        $('#avatarImg').attr('src', 'https://ui-avatars.com/api/?background=6366F1&color=fff&name=U');
        openModal('usuarioModal');
    }

    function editUsuario(id) {
        const u = usuariosData.find(x => x.id == id);
        if (!u) return;

        $('#usuarioModalTitle').text('Editar Usuario');
        $('#usuario_id').val(u.id);
        $('#usuario_nombre').val(u.nombre);
        $('#usuario_email').val(u.email);
        $('#usuario_avatar').val(u.avatar || '');
        $('#usuario_avatar_file').val('');
        $('#usuario_activo').prop('checked', u.activo == 1);
        $('#usuario_password').val('').prop('required', false);
        $('#usuario_password_confirm').val('');
        $('#passwordRequired').text('(opcional)');

        $('#avatarImg').attr('src', avatarUrl(u));

        openModal('usuarioModal');
    }

    /* ============================================================
     *  PREVISUALIZACIÓN DE AVATAR
     * ============================================================ */
    // Al elegir archivo local
    $(document).on('change', '#usuario_avatar_file', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (!/^image\//.test(file.type)) {
            Swal.fire({ icon: 'error', title: 'Archivo inválido', text: 'Selecciona una imagen' });
            $(this).val('');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Imagen muy grande', text: 'Máximo 2MB' });
            $(this).val('');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => $('#avatarImg').attr('src', e.target.result);
        reader.readAsDataURL(file);

        // Al subir archivo, limpiamos la URL para evitar confusión
        $('#usuario_avatar').val('');
    });

    // Al pegar una URL
    $(document).on('input', '#usuario_avatar', function () {
        const url = $(this).val().trim();
        if (url) {
            $('#avatarImg').attr('src', url);
            // Al usar URL, limpiamos el input file
            $('#usuario_avatar_file').val('');
        }
    });

    /* ============================================================
     *  GUARDAR USUARIO (crear / editar)  — multipart/form-data
     * ============================================================ */
    $('#usuarioForm').on('submit', function (e) {
        e.preventDefault();

        const id       = $('#usuario_id').val();
        const password = $('#usuario_password').val();
        const confirm  = $('#usuario_password_confirm').val();
        const fileEl   = document.getElementById('usuario_avatar_file');
        const file     = fileEl && fileEl.files ? fileEl.files[0] : null;

        // Validaciones cliente
        if (password && password !== confirm) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Las contraseñas no coinciden' });
            return;
        }
        if (!id && !password) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'La contraseña es requerida para nuevos usuarios' });
            return;
        }

        /* -------- FormData (multipart) -------- */
        const fd = new FormData();
        fd.append('id',       id || '');
        fd.append('nombre',   $('#usuario_nombre').val());
        fd.append('email',    $('#usuario_email').val());
        fd.append('password', password);
        fd.append('avatar',   $('#usuario_avatar').val());
        fd.append('activo',   $('#usuario_activo').is(':checked') ? 1 : 0);

        if (file) {
            fd.append('avatar_file', file);
        }

        const $btn = $(this).find('button[type="submit"]');
        const txt  = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...');

        $.ajax({
            url: '/mi/models/guardarUsuario',
            type: 'POST',
            data: fd,
            processData: false,   // 👈 CRÍTICO: no serializar FormData
            contentType: false,   // 👈 CRÍTICO: dejar que el navegador ponga el boundary
            dataType: 'json'
        })
        .done(function (response) {
            if (response && response.success) {
                Swal.fire({ icon: 'success', title: 'Guardado', showConfirmButton: false, timer: 1200 });
                closeModal('usuarioModal');
                cargarUsuarios();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (response && response.message) || 'Error al guardar'
                });
            }
        })
        .fail(function (xhr) {
            let msg = 'Error al guardar usuario';
            try {
                const r = JSON.parse(xhr.responseText);
                if (r && r.message) msg = r.message;
            } catch (err) {}
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        })
        .always(function () {
            $btn.prop('disabled', false).html(txt);
        });
    });

    /* ============================================================
     *  CAMBIAR CONTRASEÑA (admin → usuario)
     * ============================================================ */
    function openPasswordModal(id) {
        const u = usuariosData.find(x => x.id == id);
        if (!u) return;

        $('#password_usuario_id').val(u.id);
        $('#password_usuario_nombre').text(u.nombre);
        $('#password_new').val('');
        $('#password_confirm').val('');
        openModal('passwordModal');
    }

    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();

        const id      = $('#password_usuario_id').val();
        const pass    = $('#password_new').val();
        const confirm = $('#password_confirm').val();

        if (!pass || pass.length < 6) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'La contraseña debe tener al menos 6 caracteres' });
            return;
        }
        if (pass !== confirm) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Las contraseñas no coinciden' });
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        const txt  = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...');

        $.ajax({
            url: '/mi/models/cambiarPasswordUsuario',
            type: 'POST',
            data: { usuario_id: id, password_new: pass, password_confirm: confirm },
            dataType: 'json'
        })
        .done(function (response) {
            if (response && response.success) {
                Swal.fire({ icon: 'success', title: 'Contraseña actualizada', showConfirmButton: false, timer: 1200 });
                closeModal('passwordModal');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (response && response.message) || 'Error al actualizar'
                });
            }
        })
        .fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al actualizar contraseña' });
        })
        .always(function () {
            $btn.prop('disabled', false).html(txt);
        });
    });

    /* ============================================================
     *  ELIMINAR USUARIO
     * ============================================================ */
    function deleteUsuario(id) {
        const u = usuariosData.find(x => x.id == id);
        if (!u) return;

        Swal.fire({
            title: '¿Eliminar usuario?',
            text: '¿Estás seguro de que deseas eliminar a ' + u.nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '/mi/models/eliminarUsuario',
                type: 'POST',
                data: { id: id },
                dataType: 'json'
            })
            .done(function (response) {
                if (response && response.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', showConfirmButton: false, timer: 1200 });
                    cargarUsuarios();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (response && response.message) || 'Error al eliminar'
                    });
                }
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error al eliminar usuario' });
            });
        });
    }

    /* ============================================================
     *  INIT
     * ============================================================ */
    $(function () {
        cargarUsuarios();
    });

})(jQuery);