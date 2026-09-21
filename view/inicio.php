<?php
include('../config/config.php');
?>
<!doctype html>
<html lang="es">
<?php include('../layout/head.php'); ?>
<style>
    .loader {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #6366f1;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen">
    
    <?php include('../layout/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <?php include('../layout/header.php'); ?>

        <!-- Main -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card-hover bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm">Imágenes en Galería</p>
                            <h3 class="text-3xl font-bold mt-2" id="totalImagenes">0</h3>
                        </div>
                        <i class="fas fa-images text-3xl text-blue-200"></i>
                    </div>
                </div>
                
                <div class="card-hover bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-green-100 text-sm">Videos/Posts</p>
                            <h3 class="text-3xl font-bold mt-2" id="totalVideos">0</h3>
                        </div>
                        <i class="fab fa-youtube text-3xl text-green-200"></i>
                    </div>
                </div>
                
                <div class="card-hover bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-100 text-sm">Categorías Galería</p>
                            <h3 class="text-3xl font-bold mt-2" id="totalCategoriasGaleria">0</h3>
                        </div>
                        <i class="fas fa-layer-group text-3xl text-purple-200"></i>
                    </div>
                </div>
                
                <div class="card-hover bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-orange-100 text-sm">Categorías Posts</p>
                            <h3 class="text-3xl font-bold mt-2" id="totalCategoriasPodcast">0</h3>
                        </div>
                        <i class="fas fa-folder-open text-3xl text-orange-200"></i>
                    </div>
                </div>
            </div>
            
            
            <!-- Contenido reciente -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Últimos posts -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fab fa-youtube text-red-500 mr-2"></i>
                            Últimos Posts
                        </h3>
                        <a href="/mi/podcast" class="text-sm text-blue-500 hover:text-blue-600">Ver todos →</a>
                    </div>
                    <div class="space-y-3" id="ultimosPostsContainer">
                        <div class="flex justify-center py-8">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Últimas imágenes de galería -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-images text-blue-500 mr-2"></i>
                            Últimas Imágenes
                        </h3>
                        <a href="/mi/galeria" class="text-sm text-blue-500 hover:text-blue-600">Ver todos →</a>
                    </div>
                    <div class="grid grid-cols-2 gap-3" id="ultimasImagenesContainer">
                        <div class="flex justify-center col-span-2 py-8">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Categorías Galería -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-folder text-indigo-500 mr-2"></i>
                            Categorías Galería
                        </h3>
                        <a href="/mi/galeria" class="text-sm text-blue-500 hover:text-blue-600">Ver todos →</a>
                    </div>
                    <div class="space-y-2" id="categoriasGaleriaContainer">
                        <div class="flex justify-center py-8">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda fila: Categorías Posts -->
            <div class="mt-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-tags text-amber-500 mr-2"></i>
                            Categorías de podcast
                        </h3>
                        <a href="/mi/podcast" class="text-sm text-blue-500 hover:text-blue-600">Ver todos →</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="categoriasPodcastContainer">
                        <div class="flex justify-center col-span-4 py-8">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <?php include('../layout/footer.php'); ?>

    </div>
</div>

<script src="/controls/inicio.js"></script>
<?php include('../layout/script.php'); ?>
</body>
</html>