<?php 
include('../config/config.php'); 
?>
<!doctype html>
<html lang="es">
<?php include('../layout/head.php'); ?>
<style>
    .modal-transition {
        transition: all 0.3s ease;
    }
    .video-card {
        transition: all 0.3s ease;
    }
    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-open {
        animation: fadeIn 0.2s ease-out;
    }
    i, .fas, .far {
        transition: none !important;
        transform: none !important;
    }
    button i, a i {
        transition: none !important;
    }
    .loader {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #6366f1;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .featured-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
        z-index: 10;
    }
    .video-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .video-card:hover .video-overlay {
        background: rgba(0,0,0,0.5);
    }
    .play-button {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-size: 24px;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }
    .video-card:hover .play-button {
        opacity: 1;
        transform: scale(1);
    }
</style>
<body class="bg-gray-100 font-sans antialiased">
<div class="flex h-screen">
    <?php include('../layout/sidebar.php'); ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include('../layout/header.php'); ?>
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Posts y Videos</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona tus categorías y contenido de video</p>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button onclick="nuevaCategoria()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-folder-plus"></i> Nueva Categoría
                </button>
                <button onclick="openModal('postModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-plus"></i> Nuevo Post
                </button>
            </div>

            <!-- Sección de Categorías -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-indigo-600"></i> Categorías
                    </h2>
                    <button onclick="openModal('categoriaModal')" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        + Agregar
                    </button>
                </div>
                <div id="categoriasContainer" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div class="loader"></div>
                </div>
            </div>

            <!-- Sección de Posts -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-film text-purple-600"></i> Todos los Posts
                    </h2>
                    <div class="flex gap-2">
                        <select id="filtroCategoria" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                            <option value="todas">Todas las categorías</option>
                        </select>
                    </div>
                </div>
                <div id="postsContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <div class="loader"></div>
                </div>
            </div>

        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<!-- Modal: Categoría -->
<div id="categoriaModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this) closeModal('categoriaModal')">
    <div class="bg-white rounded-2xl max-w-md w-full modal-open">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-folder-plus text-indigo-600 mr-2"></i> <span id="categoriaModalTitle">Nueva Categoría</span>
            </h3>
            <button onclick="closeModal('categoriaModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="categoriaForm" class="p-5 space-y-4">
            <input type="hidden" id="categoria_id" name="categoria_id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la categoría</label>
                <input type="text" id="categoria_nombre" name="nombre" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                <textarea id="categoria_descripcion" name="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Icono (clase FontAwesome)</label>
                <input type="text" id="categoria_icono" name="icono" value="fas fa-video" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color (hex)</label>
                <input type="color" id="categoria_color" name="color" value="#6366F1" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition">Guardar Categoría</button>
                <button type="button" onclick="closeModal('categoriaModal')" class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Post (CORREGIDO) -->
<div id="postModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this) closeModal('postModal')">
    <div class="bg-white rounded-2xl max-w-2xl w-full modal-open my-8">
        <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus text-green-600 mr-2"></i> <span id="postModalTitle">Nuevo Post</span>
            </h3>
            <button onclick="closeModal('postModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="postForm" class="p-5 space-y-4">
            <input type="hidden" id="post_id" name="post_id">
            <input type="hidden" id="post_usuario_id" name="usuario_id" value="1">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                <input type="text" id="post_titulo" name="titulo" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                <textarea id="post_descripcion" name="descripcion" rows="3" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link de YouTube *</label>
                <input type="url" id="post_youtube_link" name="youtube_link" placeholder="https://www.youtube.com/watch?v=..." required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select id="post_categoria_id" name="categoria_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Seleccionar categoría</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación</label>
                    <input type="date" id="post_fecha_publicacion" name="fecha_publicacion" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duración (ej: 15:30)</label>
                <input type="text" id="post_duracion" name="duracion" placeholder="00:00" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" id="post_destacado" name="destacado" value="1" class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <label for="post_destacado" class="ml-2 block text-sm font-medium text-gray-700">Destacar este post</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="post_activo" name="activo" value="1" checked class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <label for="post_activo" class="ml-2 block text-sm font-medium text-gray-700">Activo</label>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">Guardar Post</button>
                <button type="button" onclick="closeModal('postModal')" class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ver Video -->
<div id="verModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeModal('verModal')">
    <div class="max-w-4xl w-full relative">
        <button onclick="closeModal('verModal')" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl">&times;</button>
        <div id="verVideo" class="w-full" style="aspect-ratio: 16/9;"></div>
        <p id="verTitulo" class="text-white text-center mt-4 text-lg"></p>
    </div>
</div>

<script src="/controls/podcast.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>