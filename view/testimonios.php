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
    .testimonial-card {
        transition: all 0.3s ease;
    }
    .testimonial-card:hover {
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
        border-top: 3px solid #E1306C;
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
    .instagram-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .instagram-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .testimonial-card:hover .instagram-overlay {
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
        color: #E1306C;
        font-size: 24px;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }
    .testimonial-card:hover .play-button {
        opacity: 1;
        transform: scale(1);
    }
    .instagram-embed {
        border-radius: 12px;
        overflow: hidden;
        background: #fafafa;
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
</style>
<body class="bg-gray-100 font-sans antialiased">
<div class="flex h-screen">
    <?php include('../layout/sidebar.php'); ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include('../layout/header.php'); ?>
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Testimonios Instagram</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona los reels de Instagram como testimonios</p>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button onclick="nuevoTestimonio()" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fab fa-instagram"></i> Nuevo Testimonio
                </button>
                <button onclick="recargarTestimonios()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-sync"></i> Recargar
                </button>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-video text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Reels</p>
                            <p class="text-xl font-bold" id="totalReels">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Activos</p>
                            <p class="text-xl font-bold" id="totalActivos">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Inactivos</p>
                            <p class="text-xl font-bold" id="totalInactivos">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center">
                            <i class="fab fa-instagram text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Último reel</p>
                            <p class="text-sm font-bold text-gray-700" id="ultimoReel">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Testimonios -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fab fa-instagram text-pink-600"></i> Todos los Testimonios
                    </h2>
                    <div class="flex gap-2">
                        <select id="filtroEstado" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                            <option value="todos">Todos los estados</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                        <input type="text" id="filtroBusqueda" placeholder="Buscar..." class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                    </div>
                </div>
                <div id="testimoniosContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <div class="loader"></div>
                </div>
            </div>

        </main>
        <?php include('../layout/footer.php'); ?>
    </div>
</div>

<!-- Modal: Testimonio -->
<div id="testimonioModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this) closeModal('testimonioModal')">
    <div class="bg-white rounded-2xl max-w-lg w-full modal-open my-8">
        <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fab fa-instagram text-pink-600 mr-2"></i> <span id="testimonioModalTitle">Nuevo Testimonio</span>
            </h3>
            <button onclick="closeModal('testimonioModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="testimonioForm" class="p-5 space-y-4">
            <input type="hidden" id="testimonio_id" name="testimonio_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID del Reel *</label>
                <input type="text" id="testimonio_reel" name="reel" placeholder="Ej: DL5lJOluNAI" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Ingresa solo el ID del reel (ej: DL5lJOluNAI)</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                <input type="text" id="testimonio_titulo" name="titulo" placeholder="Título del testimonio" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vista previa</label>
                <div id="previewContainer" class="border rounded-lg p-2 bg-gray-50 hidden">
                    <div class="flex items-center gap-3">
                        <div id="previewThumbnail" class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                            <i class="fab fa-instagram text-2xl text-gray-400"></i>
                        </div>
                        <div>
                            <p id="previewTitulo" class="text-sm font-medium text-gray-700">Título</p>
                            <p class="text-xs text-gray-500">Instagram Reel</p>
                            <a id="previewLink" href="#" target="_blank" class="text-xs text-pink-500 hover:underline">Ver en Instagram</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="testimonio_estado" name="estado" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white py-2 rounded-lg transition">
                    Guardar Testimonio
                </button>
                <button type="button" onclick="closeModal('testimonioModal')" class="flex-1 border border-gray-300 hover:bg-gray-50 py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Ver Testimonio -->
<div id="verModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeModal('verModal')">
    <div class="max-w-[300px] w-full relative">
        <div id="verTestimonio" class="w-full bg-white rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center">
                    <i class="fab fa-instagram text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800" id="verTitulo">Título</p>
                    <p class="text-xs text-gray-500">Instagram Reel</p>
                </div>
            </div>
            <div id="verReelContainer" class="instagram-embed">
                <div class="text-center py-8">
                    <i class="fab fa-instagram text-6xl text-gray-300"></i>
                    <p class="text-gray-500 mt-2">Cargando reel...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/controls/testimonios.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>