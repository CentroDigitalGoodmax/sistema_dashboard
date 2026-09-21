<?php
include('../config/config.php');
?>
<!doctype html>
<html lang="es">
<?php include('../layout/head.php'); ?>
<body class="bg-gray-100 font-sans antialiased">
<div class="flex h-screen">
    <?php include('../layout/sidebar.php'); ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include('../layout/header.php'); ?>
        <main class="flex-1 overflow-y-auto p-6">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
                <p class="text-gray-500 text-sm mt-1">Administra los usuarios del sistema</p>
            </div>

            <!-- Botón de acción -->
            <div class="mb-6">
                <button onclick="resetUsuarioModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Avatar</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Estado</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Último Acceso</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usuariosTableBody" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-indigo-600 mx-auto"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<!-- MODAL: Nuevo/Editar Usuario -->
<div id="usuarioModal"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4 overflow-y-auto"
     onclick="if(event.target===this) closeModal('usuarioModal')">
    <div class="bg-white rounded-2xl max-w-2xl w-full my-8 shadow-2xl">
        <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
                <span id="usuarioModalTitle">Nuevo Usuario</span>
            </h3>
            <button onclick="closeModal('usuarioModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="usuarioForm" class="p-5 space-y-4" enctype="multipart/form-data">
            <input type="hidden" id="usuario_id" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="usuario_nombre" name="nombre" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" id="usuario_email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña <span id="passwordRequired">*</span>
                    </label>
                    <input type="password" id="usuario_password" name="password" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Déjalo vacío para mantener la contraseña actual</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" id="usuario_password_confirm" name="password_confirm" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Avatar: archivo + URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>

                <div class="flex items-center gap-4 mb-3">
                    <img id="avatarImg"
                         src="https://ui-avatars.com/api/?background=6366F1&color=fff&name=U"
                         alt="Avatar"
                         class="w-16 h-16 rounded-full object-cover border border-gray-200">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">Subir imagen (máx. 2MB)</label>
                        <input type="file" id="usuario_avatar_file" name="avatar_file" accept="image/*"
                               class="block w-full text-sm text-gray-600
                                      file:mr-3 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <input type="hidden" id="usuario_avatar" name="avatar" placeholder="https://..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="usuario_activo" name="activo" value="1" checked
                       class="w-4 h-4 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500">
                <label for="usuario_activo" class="ml-2 block text-sm font-medium text-gray-700">Usuario activo</label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition font-semibold">
                    Guardar Usuario
                </button>
                <button type="button" onclick="closeModal('usuarioModal')"
                        class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Cambiar contraseña (admin → usuario) -->
<div id="passwordModal"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4"
     onclick="if(event.target===this) closeModal('passwordModal')">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-key text-indigo-600 mr-2"></i> Cambiar Contraseña
            </h3>
            <button onclick="closeModal('passwordModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="passwordForm" class="p-5 space-y-4">
            <input type="hidden" id="password_usuario_id" name="usuario_id">

            <p class="text-sm text-gray-500">
                Cambiando contraseña de: <strong id="password_usuario_nombre" class="text-gray-800"></strong>
            </p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña *</label>
                <input type="password" id="password_new" name="password_new" required minlength="6"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña *</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition font-semibold">
                    Actualizar contraseña
                </button>
                <button type="button" onclick="closeModal('passwordModal')"
                        class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script src="/controls/usuarios.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>