/* controls/perfil.js
 * Perfil del usuario logueado — Argen Medical
 * Incluye: cargar perfil, editar datos + avatar (archivo/URL), cambiar contraseña
 */
(function ($) {
    'use strict';

    console.log('[perfil.js] cargado correctamente');

    let perfilData = {};

    /* ============================================================
     *  HELPERS
     * ============================================================ */
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
        });
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '';
        const d = new Date(String(dateStr).replace(' ', 'T'));
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES') + ' ' +
               d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    function avatarUrl(u) {
        if (u && u.avatar && String(u.avatar).trim() !== '') return u.avatar;
        const nombre = (u && u.nombre) ? u.nombre : 'U';
        return 'https://ui-avatars.com/api/?background=6366F1&color=fff&name=' +
            encodeURIComponent(nombre);
    }

    // Exponer funciones usadas en onclick
    window.toggleEditMode = toggleEditMode;

    /* ============================================================
     *  CARGAR / RENDERIZAR PERFIL
     * ============================================================ */
    function cargarPerfil() {
        $.ajax({
            url: '/mi/models/obtenerPerfil',
            type: 'GET',
            dataType: 'json'
        })
        .done(function (perfil) {
            if (!perfil || perfil.error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el perfil' });
                return;
            }

            perfilData = perfil;
            renderizarPerfil(perfil);
        })
        .fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar el perfil' });
        });
    }

    function renderizarPerfil(perfil) {
        // Vista
        $('#perfilNombreView').text(perfil.nombre || '');
        $('#perfilEmailView').text(perfil.email || '');
        $('#perfilAvatar').attr('src', avatarUrl(perfil));

        // Info adicional
        $('#ultimoAcceso').text(perfil.ultimo_acceso ? formatDateTime(perfil.ultimo_acceso) : 'Nunca');
        $('#creadaEn').text(formatDateTime(perfil.created_at));
        $('#actualizadaEn').text(formatDateTime(perfil.updated_at));

        // Formulario
        $('#perfil_nombre').val(perfil.nombre || '');
        $('#perfil_email').val(perfil.email || '');
        $('#perfil_avatar').val(perfil.avatar || '');
        $('#perfil_avatar_file').val('');
        $('#avatarImgForm').attr('src', avatarUrl(perfil));
    }

    /* ============================================================
     *  TOGGLE MODO EDICIÓN
     * ============================================================ */
    function toggleEditMode() {
        const $form = $('#perfilForm');
        const $view = $('#perfilView');
        const $btn  = $('#editBtn');

        if ($form.hasClass('hidden')) {
            // Entrar a modo edición
            $form.removeClass('hidden');
            $view.addClass('hidden');
            $btn.html('<i class="fas fa-times"></i> Cancelar');
        } else {
            // Salir a modo vista
            $form.addClass('hidden');
            $view.removeClass('hidden');
            $btn.html('<i class="fas fa-edit"></i> Editar');

            // Restaurar los valores originales en el form
            if (perfilData && perfilData.nombre) {
                $('#perfil_nombre').val(perfilData.nombre);
                $('#perfil_email').val(perfilData.email);
                $('#perfil_avatar').val(perfilData.avatar || '');
                $('#perfil_avatar_file').val('');
                $('#avatarImgForm').attr('src', avatarUrl(perfilData));
            }
        }
    }

    /* ============================================================
     *  PREVISUALIZACIÓN DE AVATAR
     * ============================================================ */
    // Al elegir archivo local
    $(document).on('change', '#perfil_avatar_file', function () {
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
        reader.onload = e => $('#avatarImgForm').attr('src', e.target.result);
        reader.readAsDataURL(file);

        // Al subir archivo, limpiamos la URL para evitar confusión
        $('#perfil_avatar').val('');
    });

    // Al pegar una URL
    $(document).on('input', '#perfil_avatar', function () {
        const url = $(this).val().trim();
        if (url) {
            $('#avatarImgForm').attr('src', url);
            // Al usar URL, limpiamos el input file
            $('#perfil_avatar_file').val('');
        }
    });

    /* ============================================================
     *  GUARDAR PERFIL — multipart/form-data
     * ============================================================ */
    $('#perfilForm').on('submit', function (e) {
        e.preventDefault();

        const fileEl = document.getElementById('perfil_avatar_file');
        const file   = fileEl && fileEl.files ? fileEl.files[0] : null;

        /* -------- FormData -------- */
        const fd = new FormData();
        fd.append('nombre', $('#perfil_nombre').val());
        fd.append('email',  $('#perfil_email').val());
        fd.append('avatar', $('#perfil_avatar').val());

        if (file) {
            fd.append('avatar_file', file);
        }

        const $btn = $(this).find('button[type="submit"]');
        const txt  = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...');

        $.ajax({
            url: '/mi/models/actualizarPerfil',
            type: 'POST',
            data: fd,
            processData: false,   // 👈 CRÍTICO
            contentType: false,   // 👈 CRÍTICO
            dataType: 'json'
        })
        .done(function (response) {
            if (response && response.success) {
                Swal.fire({ icon: 'success', title: 'Perfil actualizado', showConfirmButton: false, timer: 1200 });
                cargarPerfil();
                // Cerrar modo edición
                $('#perfilForm').addClass('hidden');
                $('#perfilView').removeClass('hidden');
                $('#editBtn').html('<i class="fas fa-edit"></i> Editar');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (response && response.message) || 'Error al actualizar perfil'
                });
            }
        })
        .fail(function (xhr) {
            let msg = 'Error al actualizar perfil';
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
     *  CAMBIAR CONTRASEÑA
     * ============================================================ */
    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();

        const current = $('#password_current').val();
        const nueva   = $('#password_new').val();
        const confirm = $('#password_confirm').val();

        if (!current || !nueva || !confirm) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Completa todos los campos' });
            return;
        }

        if (nueva.length < 6) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'La nueva contraseña debe tener al menos 6 caracteres' });
            return;
        }

        if (nueva !== confirm) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Las nuevas contraseñas no coinciden' });
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        const txt  = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...');

        $.ajax({
            url: '/mi/models/cambiarPassword',
            type: 'POST',
            data: {
                password_current: current,
                password_new:     nueva
            },
            dataType: 'json'
        })
        .done(function (response) {
            if (response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Contraseña actualizada',
                    text: 'Tu contraseña ha sido cambiada exitosamente',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#passwordForm')[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: (response && response.message) || 'Error al cambiar la contraseña'
                });
            }
        })
        .fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cambiar la contraseña' });
        })
        .always(function () {
            $btn.prop('disabled', false).html(txt);
        });
    });

    /* ============================================================
     *  INIT
     * ============================================================ */
    $(function () {
        cargarPerfil();
    });

})(jQuery);