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
    .image-card {
        transition: all 0.3s ease;
    }
    .image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .dropzone {
        border: 2px dashed #cbd5e1;
        transition: all 0.3s ease;
    }
    .dropzone.dragover {
        border-color: #6366f1;
        background-color: #eef2ff;
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
</style>
<body class="bg-gray-100 font-sans antialiased">
<div class="flex h-screen">
    <?php include('../layout/sidebar.php'); ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include('../layout/header.php'); ?>
        <main class="flex-1 overflow-y-auto p-6">
            <a onclick="cargarCategorias()" class="cursor-pointer">Consultar Categoría</a>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Galería de Imágenes</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona tus categorías y fotografías</p>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button onclick="openModal('categoriaModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-folder-plus"></i> Nueva Categoría
                </button>
                <button onclick="openModal('imagenModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-upload"></i> Subir Imagen
                </button>
            </div>

            <!-- Sección de Categorías (cargada por AJAX) -->
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

            <!-- Sección de Galería (cargada por AJAX) -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-images text-green-600"></i> Todas las Imágenes
                    </h2>
                    <div class="flex gap-2">
                        <select id="filtroCategoria" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                            <option value="todas">Todas las categorías</option>
                        </select>
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-th-large text-lg"></i>
                        </button>
                    </div>
                </div>
                <div id="galeriaContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <div class="loader"></div>
                </div>
            </div>

        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<!-- Modal: Nueva Categoría -->
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
                <input type="text" id="categoria_icono" name="icono" value="fas fa-folder" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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

<!-- Modal: Subir Imagen -->
<div id="imagenModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this) closeModal('imagenModal')">
    <div class="bg-white rounded-2xl max-w-2xl w-full modal-open">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-cloud-upload-alt text-green-600 mr-2"></i> Subir Imagen
            </h3>
            <button onclick="closeModal('imagenModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="imagenForm" class="p-5 space-y-4" enctype="multipart/form-data">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título de la imagen</label>
                <input type="text" name="titulo" id="uploadTitulo" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                <select name="categoria_id" id="uploadCategoria" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Seleccionar categoría</option>
                </select>
            </div>

            <!-- dos columnas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                    <textarea name="descripcion" id="uploadDescripcion" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar imagen</label>
                    <div id="dropzone" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-green-500 transition">
                        <p class="text-gray-500">Arrastra y suelta una imagen aquí o haz clic para seleccionar</p>
                        <p class="text-xs text-gray-400 mt-2">Formatos: JPG, PNG, GIF (Max 5MB)</p>
                        <input type="file" name="imagen" id="imagenInput" accept="image/*" class="hidden" required>
                    </div>
                </div>
                <div>
                    <div id="previewContainer" class="mt-3 hidden">
                        <img id="imagePreview" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
            </div>



            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">Subir Imagen</button>
                <button type="button" onclick="closeModal('imagenModal')" class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ver Imagen -->
<div id="verModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeModal('verModal')">
    <div class="max-w-4xl w-full relative">
        <button onclick="closeModal('verModal')" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl">&times;</button>
        <img id="verImagen" src="" alt="" class="w-full rounded-lg">
        <p id="verTitulo" class="text-white text-center mt-4 text-lg"></p>
    </div>
</div>

<script src="/controls/galeria.js"></script>

<?php include('../layout/script.php'); ?>
</body>
</html>