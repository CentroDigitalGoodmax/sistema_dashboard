console.log('galeria.js cargado correctamente');

let categoriasData = [];
let imagenesData = [];

// Cargar categorías desde AJAX con jQuery
function cargarCategorias() {
    var $container = $('#categoriasContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar categorías...');
    
    $.ajax({
        url: '/mi/models/consultarCategoriasGaleria',
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
        var fotosCount = 0;
        for(var j = 0; j < imagenesData.length; j++) {
            if(imagenesData[j].categoria_id == cat.id) fotosCount++;
        }
        html += `
            <div class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition group">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background: linear-gradient(135deg, ${cat.color}20, ${cat.color}40);">
                    <i class="${cat.icono} text-2xl" style="color: ${cat.color};"></i>
                </div>
                <h3 class="font-medium text-gray-800">${escapeHtml(cat.nombre)}</h3>
                <p class="text-xs text-gray-500">${fotosCount} fotos</p>
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
    var $select = $('#uploadCategoria');
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

// Cargar galería
function cargarGaleria() {
    var $container = $('#galeriaContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar galería...');
    
    $.ajax({
        url: '/mi/models/consultarGaleria',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Galería recibida:', response);
            imagenesData = response;
            renderizarGaleria(response);
            if(categoriasData.length > 0) {
                renderizarCategorias(categoriasData);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando galería:', error);
            $container.html('<div class="col-span-full text-center text-red-500 py-8">Error al cargar imágenes: ' + error + '</div>');
        }
    });
}

// Renderizar galería
function renderizarGaleria(imagenes) {
    var $container = $('#galeriaContainer');
    
    if(!imagenes || imagenes.length === 0) {
        $container.html('<div class="col-span-full text-center text-gray-500 py-8">No hay imágenes disponibles</div>');
        return;
    }
    
    var html = '';
    for(var i = 0; i < imagenes.length; i++) {
        var img = imagenes[i];
        var catNombre = 'Sin categoría';
        var catColor = '#6366F1';
        
        for(var j = 0; j < categoriasData.length; j++) {
            if(categoriasData[j].id == img.categoria_id) {
                catNombre = categoriasData[j].nombre;
                catColor = categoriasData[j].color;
                break;
            }
        }
        
        html += `
            <div class="image-card bg-white rounded-xl shadow-sm overflow-hidden group cursor-pointer" data-categoria-id="${img.categoria_id}" onclick="verImagen('${img.imagen_url}', '${escapeHtml(img.titulo)}')">
                <div class="relative h-48 overflow-hidden">
                    <img src="${img.imagen_url}" alt="${escapeHtml(img.titulo)}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" onerror="this.src='https://via.placeholder.com/400x300?text=Error+de+imagen'">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center gap-2">
                        <button onclick="event.stopPropagation(); verImagen('${img.imagen_url}', '${escapeHtml(img.titulo)}')" class="bg-white rounded-full w-8 h-8 flex items-center justify-center text-gray-700 hover:bg-indigo-600 hover:text-white transition">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        <button onclick="event.stopPropagation(); deleteImagen(${img.id})" class="bg-white rounded-full w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3">
                    <h3 class="font-medium text-gray-800 text-sm truncate">${escapeHtml(img.titulo)}</h3>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: ${catColor}20; color: ${catColor};">${escapeHtml(catNombre)}</span>
                        <span class="text-xs text-gray-400">${formatDate(img.fecha_publicacion)}</span>
                    </div>
                    <div class="flex gap-3 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-eye"></i> ${formatNumber(img.visitas)}</span>
                        <span><i class="fas fa-heart"></i> ${formatNumber(img.likes)}</span>
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

function openModal(modalId) {
    $('#' + modalId).removeClass('hidden').addClass('flex');
}

function closeModal(modalId) {
    $('#' + modalId).addClass('hidden').removeClass('flex');
}

function verImagen(url, titulo) {
    $('#verImagen').attr('src', url);
    $('#verTitulo').text(titulo);
    openModal('verModal');
}

function editCategoria(id) {
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
        $('#categoria_icono').val(categoria.icono || 'fas fa-folder');
        $('#categoria_color').val(categoria.color || '#6366F1');
        openModal('categoriaModal');
    }
}

function deleteCategoria(id) {
    var nombre = '';
    var imagenesAsociadas = 0;
    for(var i = 0; i < categoriasData.length; i++) {
        if(categoriasData[i].id == id) {
            nombre = categoriasData[i].nombre;
            break;
        }
    }
    for(var i = 0; i < imagenesData.length; i++) {
        if(imagenesData[i].categoria_id == id) imagenesAsociadas++;
    }
    
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Se eliminarán ' + imagenesAsociadas + ' imágenes asociadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/mi/models/eliminarCategoria',
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
                    cargarGaleria();
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

function deleteImagen(id) {
    Swal.fire({
        title: '¿Eliminar imagen?',
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
                url: '/mi/models/eliminarImagen',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Imagen eliminada',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    cargarGaleria();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar imagen'
                    });
                }
            });
        }
    });
}

// Dropzone
var dropzone = document.getElementById('dropzone');
var imagenInput = document.getElementById('imagenInput');
var previewContainer = document.getElementById('previewContainer');
var imagePreview = document.getElementById('imagePreview');

if(dropzone) {
    dropzone.addEventListener('click', function() { imagenInput.click(); });
    
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    
    dropzone.addEventListener('dragleave', function() {
        dropzone.classList.remove('dragover');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        var file = e.dataTransfer.files[0];
        if(file && file.type.startsWith('image/')) {
            imagenInput.files = e.dataTransfer.files;
            previewImage(file);
        }
    });
    
    imagenInput.addEventListener('change', function(e) {
        if(e.target.files[0]) {
            previewImage(e.target.files[0]);
        }
    });
}

function previewImage(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
        imagePreview.src = e.target.result;
        previewContainer.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
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
    
    $.ajax({
        url: '/mi/models/guardarCategoria',
        type: 'POST',
        data: formData,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Categoría guardada',
                showConfirmButton: false,
                timer: 1500
            });
            closeModal('categoriaModal');
            cargarCategorias();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar categoría'
            });
        }
    });
});

$('#imagenForm').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    $.ajax({
        url: '/mi/models/subirImagen',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Imagen subida',
                text: 'La imagen se subió correctamente.',
                showConfirmButton: false,
                timer: 1500
            });
            closeModal('imagenModal');
            cargarGaleria();
            $('#imagenForm')[0].reset();
            $('#previewContainer').addClass('hidden');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al subir imagen'
            });
        }
    });
});

// Filtro
$('#filtroCategoria').on('change', function() {
    var categoriaId = $(this).val();
    var $cards = $('.image-card');
    $cards.each(function() {
        if(categoriaId === 'todas' || $(this).data('categoria-id') == categoriaId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

function resetCategoriaModal() {
    $('#categoriaModalTitle').text('Nueva Categoría');
    $('#categoria_id').val('');
    $('#categoria_nombre').val('');
    $('#categoria_descripcion').val('');
    $('#categoria_icono').val('fas fa-folder');
    $('#categoria_color').val('#6366F1');
}

var originalOpenModal = openModal;
window.openModal = function(modalId) {
    if(modalId === 'categoriaModal') {
        resetCategoriaModal();
    }
    originalOpenModal(modalId);
};

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    console.log('📌 Documento listo, iniciando carga...');
    cargarCategorias();
    cargarGaleria();
});