console.log('podcast.js cargado correctamente');

let categoriasData = [];
let postsData = [];

// Cargar categorías
function cargarCategorias() {
    var $container = $('#categoriasContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar categorías...');
    
    $.ajax({
        url: '/mi/models/consultarCategoriasPodcast',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Categorías recibidas:', response);
            categoriasData = response;
            renderizarCategorias(response);
            actualizarSelectCategorias(response);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando categorías:', error);
            $container.html('<div class="col-span-full text-center text-red-500 py-8">Error al cargar categorías: ' + error + '</div>');
        }
    });
}

// Renderizar categorías
function renderizarCategorias(categorias) {
    var $container = $('#categoriasContainer');
    
    if(!categorias || categorias.length === 0) {
        $container.html('<div class="col-span-full text-center text-gray-500 py-8">No hay categorías disponibles</div>');
        return;
    }
    
    var html = '';
    for(var i = 0; i < categorias.length; i++) {
        var cat = categorias[i];
        var postsCount = 0;
        for(var j = 0; j < postsData.length; j++) {
            if(postsData[j].categoria_id == cat.id) postsCount++;
        }
        html += `
            <div class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition group">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background: linear-gradient(135deg, ${cat.color}20, ${cat.color}40);">
                    <i class="${cat.icono} text-2xl" style="color: ${cat.color};"></i>
                </div>
                <h3 class="font-medium text-gray-800">${escapeHtml(cat.nombre)}</h3>
                <p class="text-xs text-gray-500">${postsCount} posts</p>
                <div class="mt-2 opacity-0 group-hover:opacity-100 transition">
                    <button onclick="editCategoria(${cat.id})" class="text-blue-500 hover:text-blue-600 text-xs mx-1">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteCategoria(${cat.id})" class="text-red-500 hover:text-red-600 text-xs mx-1">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }
    $container.html(html);
}

// Actualizar select de categorías
function actualizarSelectCategorias(categorias) {
    var $select = $('#post_categoria_id');
    var options = '<option value="">Seleccionar categoría</option>';
    for(var i = 0; i < categorias.length; i++) {
        options += `<option value="${categorias[i].id}">${escapeHtml(categorias[i].nombre)}</option>`;
    }
    $select.html(options);
    
    var $filtroSelect = $('#filtroCategoria');
    var filtroOptions = '<option value="todas">Todas las categorías</option>';
    for(var i = 0; i < categorias.length; i++) {
        filtroOptions += `<option value="${categorias[i].id}">${escapeHtml(categorias[i].nombre)}</option>`;
    }
    $filtroSelect.html(filtroOptions);
}

// Cargar posts
function cargarPosts() {
    var $container = $('#postsContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar posts...');
    
    $.ajax({
        url: '/mi/models/consultarPodcast',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Posts recibidos:', response);
            postsData = response;
            renderizarPosts(response);
            if(categoriasData.length > 0) {
                renderizarCategorias(categoriasData);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando posts:', error);
            $container.html('<div class="col-span-full text-center text-red-500 py-8">Error al cargar posts: ' + error + '</div>');
        }
    });
}

// Obtener ID de video de YouTube
function getYouTubeVideoId(url) {
    if(!url) return null;
    var match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^\&\n\r]+)/);
    return match && match[1] ? match[1] : null;
}

// Obtener thumbnail
function getYouTubeThumbnail(videoId) {
    return `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`;
}

// Renderizar posts
function renderizarPosts(posts) {
    var $container = $('#postsContainer');
    
    if(!posts || posts.length === 0) {
        $container.html('<div class="col-span-full text-center text-gray-500 py-8">No hay posts disponibles</div>');
        return;
    }
    
    var html = '';
    for(var i = 0; i < posts.length; i++) {
        var post = posts[i];
        var catNombre = 'Sin categoría';
        var catColor = '#6366F1';
        
        for(var j = 0; j < categoriasData.length; j++) {
            if(categoriasData[j].id == post.categoria_id) {
                catNombre = categoriasData[j].nombre;
                catColor = categoriasData[j].color;
                break;
            }
        }
        
        var videoId = getYouTubeVideoId(post.youtube_link);
        var thumbnail = post.thumbnail_url || (videoId ? getYouTubeThumbnail(videoId) : 'https://via.placeholder.com/400x300?text=Video+no+disponible');
        var destacado = post.destacado == 1 ? '<div class="featured-badge">⭐ DESTACADO</div>' : '';
        
        html += `
            <div class="video-card bg-white rounded-xl shadow-sm overflow-hidden group cursor-pointer" data-categoria-id="${post.categoria_id}" onclick="verPost(${post.id}, '${post.youtube_link}', '${escapeHtml(post.titulo)}')">
                <div class="relative h-48 overflow-hidden">
                    ${destacado}
                    <img src="${thumbnail}" alt="${escapeHtml(post.titulo)}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/400x300?text=Video+no+disponible'">
                    <div class="video-overlay">
                        <div class="play-button">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        ${post.duracion || '00:00'}
                    </div>
                </div>
                <div class="p-3">
                    <h3 class="font-medium text-gray-800 text-sm truncate">${escapeHtml(post.titulo)}</h3>
                    <p class="text-xs text-gray-600 truncate">${escapeHtml(post.descripcion)}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: ${catColor}20; color: ${catColor};">${escapeHtml(catNombre)}</span>
                        <span class="text-xs text-gray-400">${formatDate(post.fecha_publicacion)}</span>
                    </div>
                    <div class="flex gap-3 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-eye"></i> ${formatNumber(post.visitas)}</span>
                        <span><i class="fas fa-heart"></i> ${formatNumber(post.likes)}</span>
                    </div>
                    <div class="flex gap-2 mt-3 text-xs">
                        <button onclick="event.stopPropagation(); editPost(${post.id})" class="flex-1 text-blue-500 hover:text-blue-600 hover:bg-blue-50 py-1 rounded transition">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button onclick="event.stopPropagation(); deletePost(${post.id})" class="flex-1 text-red-500 hover:text-red-600 hover:bg-red-50 py-1 rounded transition">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    $container.html(html);
}

function formatNumber(num) {
    if(!num) return '0';
    var n = parseInt(num);
    if(n >= 1000) return (n/1000).toFixed(1) + 'k';
    return n.toString();
}

function formatDate(dateStr) {
    if(!dateStr) return '';
    var date = new Date(dateStr);
    return date.toLocaleDateString('es-ES');
}

function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// *** FUNCIONES DE MODAL CORREGIDAS ***
function openModal(modalId) {
    $('#' + modalId).removeClass('hidden').addClass('flex');
}

function closeModal(modalId) {
    $('#' + modalId).addClass('hidden').removeClass('flex');
}

function verPost(id, youtubeLink, titulo) {
    var videoId = getYouTubeVideoId(youtubeLink);
    if(videoId) {
        var iframe = `<iframe width="100%" height="100%" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 8px;"></iframe>`;
        $('#verVideo').html(iframe);
    } else {
        $('#verVideo').html('<div class="text-white text-center">No se pudo cargar el video</div>');
    }
    $('#verTitulo').text(titulo);
    openModal('verModal');
}

// FUNCIÓN PARA ABRIR NUEVA CATEGORÍA (MODIFICADA)
function nuevaCategoria() {
    console.log('🆕 Abriendo modal para nueva categoría');
    resetCategoriaModal(); // Limpiar el formulario
    $('#categoriaModalTitle').text('Nueva Categoría'); // Asegurar el título
    openModal('categoriaModal');
}

// FUNCIÓN PARA EDITAR CATEGORÍA (MODIFICADA)
function editCategoria(id) {
    console.log('✏️ Editando categoría ID:', id);
    var categoria = null;
    for(var i = 0; i < categoriasData.length; i++) {
        if(categoriasData[i].id == id) {
            categoria = categoriasData[i];
            break;
        }
    }
    if(categoria) {
        $('#categoriaModalTitle').text('Editar Categoría');
        $('#categoria_id').val(categoria.id);
        $('#categoria_nombre').val(categoria.nombre);
        $('#categoria_descripcion').val(categoria.descripcion || '');
        $('#categoria_icono').val(categoria.icono || 'fas fa-video');
        $('#categoria_color').val(categoria.color || '#6366F1');
        openModal('categoriaModal');
    } else {
        console.error('❌ Categoría no encontrada');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se encontró la categoría para editar'
        });
    }
}

function deleteCategoria(id) {
    var postsAsociados = 0;
    for(var i = 0; i < postsData.length; i++) {
        if(postsData[i].categoria_id == id) postsAsociados++;
    }
    
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Se eliminarán ' + postsAsociados + ' posts asociados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/mi/models/eliminarCategoriaPodcast',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Categoría eliminada',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    cargarCategorias();
                    cargarPosts();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar categoría'
                    });
                }
            });
        }
    });
}

function editPost(id) {
    console.log('🔍 Editando post ID:', id);
    
    // Buscar el post en postsData
    var post = null;
    for(var i = 0; i < postsData.length; i++) {
        if(postsData[i].id == id) {
            post = postsData[i];
            break;
        }
    }
    
    if(post) {
        console.log('Post encontrado:', post);
        
        // Limpiar el modal primero
        resetPostModal();
        
        // Llenar los campos con los datos del post
        $('#postModalTitle').text('Editar Post');
        $('#post_id').val(post.id);
        $('#post_titulo').val(post.titulo);
        $('#post_descripcion').val(post.descripcion || '');
        $('#post_youtube_link').val(post.youtube_link);
        
        if(post.categoria_id) {
            $('#post_categoria_id').val(post.categoria_id);
        } else {
            $('#post_categoria_id').val('');
        }
        
        $('#post_duracion').val(post.duracion || '');
        
        // Formatear fecha para el input date
        if(post.fecha_publicacion) {
            var fecha = new Date(post.fecha_publicacion);
            if(!isNaN(fecha.getTime())) {
                var fechaFormateada = fecha.toISOString().split('T')[0];
                $('#post_fecha_publicacion').val(fechaFormateada);
            } else {
                $('#post_fecha_publicacion').val('');
            }
        } else {
            $('#post_fecha_publicacion').val('');
        }
        
        $('#post_destacado').prop('checked', post.destacado == 1);
        $('#post_activo').prop('checked', post.activo == 1);
        
        // Abrir el modal directamente sin pasar por otra función que pueda causar recursión
        openModal('postModal');
    } else {
        console.error('❌ Post no encontrado en postsData');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se encontró el post para editar'
        });
    }
}

function deletePost(id) {
    Swal.fire({
        title: '¿Eliminar post?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/mi/models/eliminarPodcast',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Post eliminado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    cargarPosts();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar post'
                    });
                }
            });
        }
    });
}

// Form handlers
$('#categoriaForm').on('submit', function(e) {
    e.preventDefault();
    var formData = {
        id: $('#categoria_id').val(),
        nombre: $('#categoria_nombre').val(),
        descripcion: $('#categoria_descripcion').val(),
        icono: $('#categoria_icono').val(),
        color: $('#categoria_color').val()
    };
    
    console.log('📤 Enviando datos categoría:', formData);
    
    $.ajax({
        url: '/mi/models/guardarCategoriaPodcast',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta categoría:', response);
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Categoría guardada',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                closeModal('categoriaModal');
                cargarCategorias();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar categoría: ' + error
            });
        }
    });
});

$('#postForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = {
        id: $('#post_id').val(),
        titulo: $('#post_titulo').val(),
        descripcion: $('#post_descripcion').val(),
        youtube_link: $('#post_youtube_link').val(),
        categoria_id: $('#post_categoria_id').val(),
        usuario_id: $('#post_usuario_id').val(),
        duracion: $('#post_duracion').val(),
        fecha_publicacion: $('#post_fecha_publicacion').val(),
        destacado: $('#post_destacado').is(':checked') ? 1 : 0,
        activo: $('#post_activo').is(':checked') ? 1 : 0
    };
    
    console.log('📤 Enviando datos:', formData);
    
    $.ajax({
        url: '/mi/models/guardarPodcast',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta:', response);
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Post guardado',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                closeModal('postModal');
                cargarPosts();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar post: ' + error
            });
        }
    });
});

// Filtro
$('#filtroCategoria').on('change', function() {
    var categoriaId = $(this).val();
    var $cards = $('.video-card');
    if(categoriaId === 'todas') {
        $cards.show();
    } else {
        $cards.each(function() {
            if($(this).data('categoria-id') == categoriaId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

// Función para resetear el modal de categoría (MEJORADA)
function resetCategoriaModal() {
    console.log('🔄 Resetear modal de categoría');
    $('#categoriaModalTitle').text('Nueva Categoría');
    $('#categoria_id').val('');
    $('#categoria_nombre').val('');
    $('#categoria_descripcion').val('');
    $('#categoria_icono').val('fas fa-video');
    $('#categoria_color').val('#6366F1');
    // Limpiar cualquier mensaje de error si los hay
    $('.error-message').remove();
}

function resetPostModal() {
    $('#postModalTitle').text('Nuevo Post');
    $('#post_id').val('');
    $('#post_titulo').val('');
    $('#post_descripcion').val('');
    $('#post_youtube_link').val('');
    $('#post_categoria_id').val('');
    $('#post_duracion').val('');
    var hoy = new Date();
    var fechaHoy = hoy.toISOString().split('T')[0];
    $('#post_fecha_publicacion').val(fechaHoy);
    $('#post_destacado').prop('checked', false);
    $('#post_activo').prop('checked', true);
}

// *** FUNCIÓN PARA ABRIR MODAL CON RESET (SIN RECURSIÓN) ***
window.abrirModalConReset = function(modalId) {
    console.log('🔓 Abriendo modal con reset:', modalId);
    if(modalId === 'categoriaModal') {
        resetCategoriaModal();
        openModal('categoriaModal');
    } else if(modalId === 'postModal') {
        resetPostModal();
        openModal('postModal');
    }
};

// Inicializar
$(document).ready(function() {
    console.log('📌 Documento listo, iniciando carga...');
    cargarCategorias();
    cargarPosts();
    
    // Bind para los botones que abren modales con reset
    $(document).on('click', '[data-open-modal]', function() {
        var modalId = $(this).data('open-modal');
        if(modalId === 'categoriaModal') {
            resetCategoriaModal();
            openModal('categoriaModal');
        } else if(modalId === 'postModal') {
            resetPostModal();
            openModal('postModal');
        }
    });
    
    // Bind para el botón específico de "Nueva Categoría" si existe con ese ID
    $(document).on('click', '#btnNuevaCategoria, .btn-nueva-categoria', function() {
        nuevaCategoria();
    });
});