console.log('inicio.js cargado correctamente');

$(document).ready(function() {
    cargarDatos();
});

function cargarDatos() {
    // Cargar galería
    $.ajax({
        url: '/mi/models/consultarGaleria',
        type: 'GET',
        dataType: 'json',
        success: function(galeria) {
            $('#totalImagenes').text(galeria.length);
            mostrarUltimasImagenes(galeria.slice(0, 4));
        }
    });

    // Cargar posts
    $.ajax({
        url: '/mi/models/consultarPodcast',
        type: 'GET',
        dataType: 'json',
        success: function(posts) {
            $('#totalVideos').text(posts.length);
            mostrarUltimosPost(posts.slice(0, 5));
        }
    });

    // Cargar categorías galería
    $.ajax({
        url: '/mi/models/consultarCategoriasGaleria',
        type: 'GET',
        dataType: 'json',
        success: function(categorias) {
            $('#totalCategoriasGaleria').text(categorias.length);
            mostrarCategoriasGaleria(categorias.slice(0, 5));
        }
    });

    // Cargar categorías posts
    $.ajax({
        url: '/mi/models/consultarCategoriasPodcast',
        type: 'GET',
        dataType: 'json',
        success: function(categorias) {
            $('#totalCategoriasPodcast').text(categorias.length);
            mostrarCategoriasPodcast(categorias);
        }
    });
}

function mostrarUltimosPost(posts) {
    var html = '';
    if(posts.length === 0) {
        html = '<p class="text-center text-gray-500 py-6">No hay posts disponibles</p>';
    } else {
        $.each(posts, function(i, post) {
            var fecha = new Date(post.fecha_publicacion);
            html += `
                <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition cursor-pointer">
                    <i class="fab fa-youtube text-red-500 text-2xl mt-1"></i>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800 text-sm">${post.titulo}</h4>
                        <div class="flex items-center text-xs text-gray-500 mt-1">
                            <span class="bg-gray-100 rounded-full px-2 py-0.5">${post.categoria_nombre || 'Sin categoría'}</span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-eye mr-1"></i> ${post.visitas || 0}</span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">
                        ${fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' })}
                    </div>
                </div>
            `;
        });
    }
    $('#ultimosPostsContainer').html(html);
}

function mostrarUltimasImagenes(imagenes) {
    var html = '';
    if(imagenes.length === 0) {
        html = '<p class="col-span-2 text-center text-gray-500 py-6">No hay imágenes disponibles</p>';
    } else {
        $.each(imagenes, function(i, img) {
            html += `
                <div class="relative group cursor-pointer">
                    <img src="${img.imagen_url || 'https://via.placeholder.com/200x150?text=Sin+imagen'}" 
                         alt="${img.titulo}"
                         class="w-full h-24 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition rounded-lg flex items-center justify-center">
                        <div class="text-center text-white opacity-0 group-hover:opacity-100 transition">
                            <p class="text-xs font-medium">${img.titulo}</p>
                            <p class="text-xs"><i class="fas fa-eye"></i> ${img.visitas || 0}</p>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    $('#ultimasImagenesContainer').html(html);
}

function mostrarCategoriasGaleria(categorias) {
    var html = '';
    if(categorias.length === 0) {
        html = '<p class="text-center text-gray-500 py-6">No hay categorías disponibles</p>';
    } else {
        $.each(categorias, function(i, cat) {
            var colores = ['bg-blue-100', 'bg-green-100', 'bg-purple-100', 'bg-pink-100', 'bg-yellow-100'];
            var colorTexto = ['text-blue-700', 'text-green-700', 'text-purple-700', 'text-pink-700', 'text-yellow-700'];
            var idx = i % colores.length;
            html += `
                <div class="${colores[idx]} ${colorTexto[idx]} p-3 rounded-lg">
                    <div class="font-semibold text-sm">${cat.nombre}</div>
                    <div class="text-xs mt-1">${cat.foto_count || 0} imágenes</div>
                </div>
            `;
        });
    }
    $('#categoriasGaleriaContainer').html(html);
}

function mostrarCategoriasPodcast(categorias) {
    var html = '';
    if(categorias.length === 0) {
        html = '<p class="col-span-4 text-center text-gray-500 py-6">No hay categorías disponibles</p>';
    } else {
        $.each(categorias, function(i, cat) {
            var colores = ['bg-red-100', 'bg-orange-100', 'bg-amber-100', 'bg-lime-100', 'bg-cyan-100', 'bg-violet-100'];
            var colorTexto = ['text-red-700', 'text-orange-700', 'text-amber-700', 'text-lime-700', 'text-cyan-700', 'text-violet-700'];
            var idx = i % colores.length;
            html += `
                <div class="${colores[idx]} ${colorTexto[idx]} p-4 rounded-lg">
                    <div class="font-semibold">${cat.nombre}</div>
                    <div class="text-sm mt-1">${cat.post_count || 0} posts</div>
                </div>
            `;
        });
    }
    $('#categoriasPodcastContainer').html(html);
}

