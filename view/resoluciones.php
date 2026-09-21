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
    .resolucion-card {
        transition: all 0.3s ease;
    }
    .resolucion-card:hover {
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
        border-top: 3px solid #8B5CF6;
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
    .image-preview-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f3f4f6;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed #d1d5db;
    }
    .image-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview-container .placeholder {
        color: #9ca3af;
        text-align: center;
    }
    .image-preview-container .placeholder i {
        font-size: 48px;
        display: block;
        margin-bottom: 8px;
    }
    .estado-badge {
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .estado-activo {
        background: #dcfce7;
        color: #166534;
    }
    .estado-inactivo {
        background: #fee2e2;
        color: #991b1b;
    }
    .resolucion-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .resolucion-card:hover .resolucion-img {
        transform: scale(1.05);
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
                <h1 class="text-2xl font-bold text-gray-800">Resoluciones</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona las resoluciones y documentos importantes</p>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button onclick="nuevaResolucion()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-plus"></i> Nueva Resolución
                </button>
                <button onclick="recargarResoluciones()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-sync"></i> Recargar
                </button>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-file-alt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Resoluciones</p>
                            <p class="text-xl font-bold" id="totalResoluciones">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Activas</p>
                            <p class="text-xl font-bold" id="totalActivas">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Inactivas</p>
                            <p class="text-xl font-bold" id="totalInactivas">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Última actualización</p>
                            <p class="text-sm font-bold text-gray-700" id="ultimaActualizacion">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Resoluciones -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-red-600"></i> Todas las Resoluciones
                    </h2>
                    <div class="flex gap-2">
                        <select id="filtroEstado" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                            <option value="todos">Todos los estados</option>
                            <option value="1">Activas</option>
                            <option value="0">Inactivas</option>
                        </select>
                        <input type="text" id="filtroBusqueda" placeholder="Buscar..." class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    </div>
                </div>
                <div id="resolucionesContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    <div class="loader"></div>
                </div>
            </div>

        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<!-- Modal: Resolución -->
<div id="resolucionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this) closeModal('resolucionModal')">
    <div class="bg-white rounded-2xl max-w-lg w-full modal-open my-8">
        <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-file-alt text-purple-600 mr-2"></i> <span id="resolucionModalTitle">Nueva Resolución</span>
            </h3>
            <button onclick="closeModal('resolucionModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="resolucionForm" class="p-5 space-y-4" enctype="multipart/form-data">
            <input type="hidden" id="resolucion_id" name="resolucion_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Persona *</label>
                <input type="text" id="resolucion_persona" name="persona" placeholder="Nombre de la persona" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagen *</label>
                <div class="image-preview-container" id="imagePreviewContainer">
                    <div class="placeholder" id="imagePlaceholder">
                        <i class="fas fa-image"></i>
                        <p>Selecciona una imagen</p>
                        <p class="text-xs mt-1">JPG, PNG, GIF, WEBP (máx 5MB)</p>
                    </div>
                    <img id="imagePreview" src="" alt="Vista previa" style="display: none;">
                </div>
                <input type="file" id="resolucion_imagen" name="imagen" accept="image/*" class="w-full mt-2 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF, WEBP. Máximo 5MB</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="resolucion_estado" name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg transition">
                    Guardar Resolución
                </button>
                <button type="button" onclick="closeModal('resolucionModal')" class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ver Resolución -->
<div id="verModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeModal('verModal')">
    <div class="max-w-4xl w-full relative">
        <button onclick="closeModal('verModal')" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl">&times;</button>
        <div class="bg-white rounded-xl overflow-hidden">
            <img id="verImagen" src="" alt="Resolución" class="w-full h-auto max-h-[70vh] object-contain">
            <div class="p-4">
                <h3 id="verPersona" class="text-xl font-bold text-gray-800"></h3>
                <p class="text-gray-500 text-sm mt-1" id="verFecha"></p>
            </div>
        </div>
    </div>
</div>

<script src="/controls/resoluciones.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>