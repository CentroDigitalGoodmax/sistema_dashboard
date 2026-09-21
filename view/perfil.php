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
                <h1 class="text-2xl font-bold text-gray-800">Mi Perfil</h1>
                <p class="text-gray-500 text-sm mt-1">Administra tu información personal</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Información Personal -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-user text-indigo-600"></i> Información Personal
                            </h2>
                            <button onclick="toggleEditMode()" id="editBtn"
                                    class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>

                        <!-- FORMULARIO -->
                        <form id="perfilForm" class="space-y-4 hidden" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                    <input type="text" id="perfil_nombre" name="nombre" required
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" id="perfil_email" name="email" required
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Avatar: archivo + URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>

                                <div class="flex items-center gap-4 mb-3">
                                    <img id="avatarImgForm"
                                         src="https://ui-avatars.com/api/?background=6366F1&color=fff&name=U"
                                         alt="Avatar"
                                         class="w-16 h-16 rounded-full object-cover border border-gray-200">

                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 mb-1">Subir imagen (máx. 2MB)</label>
                                        <input type="file" id="perfil_avatar_file" name="avatar_file" accept="image/*"
                                               class="block w-full text-sm text-gray-600
                                                      file:mr-3 file:py-2 file:px-4
                                                      file:rounded-lg file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-indigo-50 file:text-indigo-700
                                                      hover:file:bg-indigo-100 cursor-pointer">
                                    </div>
                                </div>

                                <input type="hidden" id="perfil_avatar" name="avatar" placeholder="https://..."
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>

                            <div class="flex gap-3 pt-4 border-t">
                                <button type="submit"
                                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition font-semibold">
                                    Guardar Cambios
                                </button>
                                <button type="button" onclick="toggleEditMode()"
                                        class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">
                                    Cancelar
                                </button>
                            </div>
                        </form>

                        <!-- VISTA -->
                        <div id="perfilView" class="space-y-4">
                            <div class="flex items-center gap-4 pb-4 border-b">
                                <img id="perfilAvatar"
                                     src="https://ui-avatars.com/api/?background=6366F1&color=fff&name=U"
                                     class="w-20 h-20 rounded-full object-cover border border-gray-200">
                                <div>
                                    <p id="perfilNombreView" class="text-lg font-semibold text-gray-800"></p>
                                    <p id="perfilEmailView" class="text-sm text-gray-500"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="space-y-6">

                    <!-- Cambiar contraseña -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                            <i class="fas fa-lock text-yellow-600"></i> Cambiar Contraseña
                        </h3>
                        <form id="passwordForm" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual *</label>
                                <input type="password" id="password_current" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña *</label>
                                <input type="password" id="password_new" required minlength="6"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña *</label>
                                <input type="password" id="password_confirm" required minlength="6"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>
                            <button type="submit"
                                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg transition text-sm font-semibold">
                                Actualizar Contraseña
                            </button>
                        </form>
                    </div>

                    <!-- Info cuenta -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                            <i class="fas fa-info-circle text-blue-600"></i> Información de Cuenta
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Último acceso:</span>
                                <span id="ultimoAcceso" class="font-medium text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cuenta creada:</span>
                                <span id="creadaEn" class="font-medium text-gray-800"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Última actualización:</span>
                                <span id="actualizadaEn" class="font-medium text-gray-800"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<script src="/controls/perfil.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>