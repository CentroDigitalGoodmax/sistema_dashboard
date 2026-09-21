console.log('testimonios.js cargado correctamente');

let testimoniosData = [];

// Cargar testimonios
function cargarTestimonios() {
    var $container = $('#testimoniosContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar testimonios...');
    
    $.ajax({
        url: '/mi/models/consultarInstagram',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Testimonios recibidos:', response);
            testimoniosData = response;
            renderizarTestimonios(response);
            actualizarEstadisticas(response);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando testimonios:', error);
            $container.html('<div class="col-span-full text-center text-red-500 py-8">Error al cargar testimonios: ' + error + '</div>');
        }
    });
}

// Actualizar estadísticas
function actualizarEstadisticas(testimonios) {
    if(!testimonios || testimonios.length === 0) {
        $('#totalReels').text('0');
        $('#totalActivos').text('0');
        $('#totalInactivos').text('0');
        $('#ultimoReel').text('-');
        return;
    }
    
    var total = testimonios.length;
    var activos = 0;
    var inactivos = 0;
    
    for(var i = 0; i < testimonios.length; i++) {
        if(testimonios[i].estado == 1) {
            activos++;
        } else {
            inactivos++;
        }
    }
    
    $('#totalReels').text(total);
    $('#totalActivos').text(activos);
    $('#totalInactivos').text(inactivos);
    
    // Último reel (el más reciente por ID o fecha)
    if(testimonios.length > 0) {
        var ultimo = testimonios[testimonios.length - 1];
        $('#ultimoReel').text(ultimo.titulo.substring(0, 20) + (ultimo.titulo.length > 20 ? '...' : ''));
    }
}

// Renderizar testimonios con estilo similar al componente React
function renderizarTestimonios(testimonios) {
    var $container = $('#testimoniosContainer');
    
    if(!testimonios || testimonios.length === 0) {
        $container.html('<div class="col-span-full text-center text-gray-500 py-8">No hay testimonios disponibles</div>');
        return;
    }
    
    var html = '';
    for(var i = 0; i < testimonios.length; i++) {
        var test = testimonios[i];
        var estadoClase = test.estado == 1 ? 'estado-activo' : 'estado-inactivo';
        var estadoTexto = test.estado == 1 ? 'Activo' : 'Inactivo';
        
        // URLs de Instagram
        var instagramUrl = `https://www.instagram.com/reel/${test.reel}/`;
        var embedUrl = `https://www.instagram.com/reel/${test.reel}/embed`;
        
        html += `
            <div class="testimonial-card bg-white rounded-xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl transition-all duration-300" 
                 data-estado="${test.estado}" 
                 onclick="verTestimonio('${test.reel}', '${escapeHtml(test.titulo)}')">
                <div class="relative h-64 overflow-hidden bg-black">
                    <!-- Thumbnail con embed de Instagram -->
                    <div class="absolute inset-0 overflow-hidden" style="pointer-events: none;">
                        <iframe
                            src="${embedUrl}"
                            class="w-full h-full"
                            style="transform: scale(1.2); transform-origin: center center; opacity: 0.9; pointer-events: none;"
                            frameborder="0"
                            scrolling="no"
                            allowfullscreen
                            title="Thumbnail ${escapeHtml(test.titulo)}"
                        ></iframe>
                    </div>
                    
                    <!-- Overlay oscuro -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/20"></div>
                    
                    <!-- Badge de Instagram -->
                    <div class="absolute top-3 left-3">
                        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-black/60 backdrop-blur-sm border border-white/10">
                            <i class="fab fa-instagram text-white text-sm"></i>
                            <span class="text-white/80 text-xs font-medium">Reel</span>
                        </div>
                    </div>
                    
                    <!-- Botón de play centrado -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center shadow-2xl hover:shadow-secondary/30 transition-shadow">
                                <i class="fas fa-play text-white text-xl ml-1"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay que aparece en hover -->
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-sm font-medium px-4 py-2 rounded-full bg-black/60 backdrop-blur-sm border border-white/20">
                            Ver testimonio completo
                        </span>
                    </div>
                    
                    <!-- Estado del testimonio -->
                    <div class="absolute top-3 right-3">
                        <span class="estado-badge ${estadoClase} text-xs px-2 py-1 rounded-full">
                            ${estadoTexto}
                        </span>
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="font-medium text-gray-800 text-sm truncate">${escapeHtml(test.titulo)}</h3>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fab fa-instagram text-pink-500"></i>
                            ID: ${escapeHtml(test.reel)}
                        </span>
                        <span class="text-xs text-gray-400">
                            ${formatDate(test.created_at)}
                        </span>
                    </div>
                    <div class="flex gap-2 mt-3 text-xs border-t border-gray-100 pt-3">
                        <button onclick="event.stopPropagation(); editTestimonio(${test.id})" class="flex-1 text-blue-500 hover:text-blue-600 hover:bg-blue-50 py-1.5 rounded transition flex items-center justify-center gap-1">
                            <i class="fas fa-edit text-xs"></i> Editar
                        </button>
                        <button onclick="event.stopPropagation(); deleteTestimonio(${test.id})" class="flex-1 text-red-500 hover:text-red-600 hover:bg-red-50 py-1.5 rounded transition flex items-center justify-center gap-1">
                            <i class="fas fa-trash text-xs"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    $container.html(html);
    
    // Aplicar filtros después de renderizar
    aplicarFiltros();
}

function formatDate(dateStr) {
    if(!dateStr) return '';
    var date = new Date(dateStr);
    var now = new Date();
    var diff = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if(diff === 0) return 'Hoy';
    if(diff === 1) return 'Ayer';
    if(diff < 7) return diff + ' días';
    if(diff < 30) return Math.floor(diff / 7) + ' semanas';
    if(diff < 365) return Math.floor(diff / 30) + ' meses';
    return Math.floor(diff / 365) + ' años';
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

// *** FUNCIONES DE MODAL ***
function openModal(modalId) {
    $('#' + modalId).removeClass('hidden').addClass('flex');
}

function closeModal(modalId) {
    $('#' + modalId).addClass('hidden').removeClass('flex');
}

// Nueva función para abrir modal de testimonio
function nuevoTestimonio() {
    console.log('🆕 Abriendo modal para nuevo testimonio');
    resetTestimonioModal();
    $('#testimonioModalTitle').text('Nuevo Testimonio');
    openModal('testimonioModal');
}

// Función para recargar testimonios
function recargarTestimonios() {
    cargarTestimonios();
    Swal.fire({
        icon: 'info',
        title: 'Recargando...',
        text: 'Los testimonios se han actualizado',
        showConfirmButton: false,
        timer: 1500
    });
}

// Función para editar testimonio
function editTestimonio(id) {
    console.log('✏️ Editando testimonio ID:', id);
    var testimonio = null;
    for(var i = 0; i < testimoniosData.length; i++) {
        if(testimoniosData[i].id == id) {
            testimonio = testimoniosData[i];
            break;
        }
    }
    if(testimonio) {
        $('#testimonioModalTitle').text('Editar Testimonio');
        $('#testimonio_id').val(testimonio.id);
        $('#testimonio_reel').val(testimonio.reel);
        $('#testimonio_titulo').val(testimonio.titulo);
        $('#testimonio_estado').val(testimonio.estado);
        mostrarPreview(testimonio.reel, testimonio.titulo);
        openModal('testimonioModal');
    } else {
        console.error('❌ Testimonio no encontrado');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se encontró el testimonio para editar'
        });
    }
}

// Función para eliminar testimonio
function deleteTestimonio(id) {
    Swal.fire({
        title: '¿Eliminar testimonio?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/mi/models/eliminarInstagram',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Testimonio eliminado',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        cargarTestimonios();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error al eliminar testimonio'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar testimonio'
                    });
                }
            });
        }
    });
}

// Función para ver testimonio (MODIFICADA - usa embed real)
function verTestimonio(reelId, titulo) {
    $('#verTitulo').text(titulo);
    
    var embedUrl = `https://www.instagram.com/reel/${reelId}/embed`;
    var instagramUrl = `https://www.instagram.com/reel/${reelId}/`;
    $('#verLink').attr('href', instagramUrl);
    
    // Usar el embed directo de Instagram
    $('#verReelContainer').html(`
        <div class="relative" style="min-height: 400px; height: 60vh;">
            <iframe
                src="${embedUrl}"
                class="absolute inset-0 w-full h-full"
                frameborder="0"
                scrolling="no"
                allowfullscreen
                style="border-radius: 8px;"
                title="${escapeHtml(titulo)}"
            ></iframe>
        </div>
        <div class="mt-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center">
                    <i class="fab fa-instagram text-white text-sm"></i>
                </div>
                <span class="text-sm text-gray-600">@argenmedical</span>
            </div>
            <a href="${instagramUrl}" target="_blank" class="text-pink-500 hover:text-pink-600 text-sm flex items-center gap-1">
                Ver en Instagram <i class="fas fa-external-link-alt ml-1"></i>
            </a>
        </div>
    `);
    
    openModal('verModal');
}

// Función para mostrar preview
function mostrarPreview(reel, titulo) {
    if(reel && titulo) {
        $('#previewContainer').removeClass('hidden');
        $('#previewTitulo').text(titulo);
        $('#previewLink').attr('href', `https://www.instagram.com/reel/${reel}/embed`);
        
        // Mostrar embed como preview
        var embedUrl = `https://www.instagram.com/reel/${reel}/embed`;
        $('#previewThumbnail').html(`
            <div class="w-full h-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                <iframe
                    src="${embedUrl}"
                    class="absolute inset-0 w-full h-full"
                    style="transform: scale(1.1); pointer-events: none; opacity: 0.8;"
                    frameborder="0"
                    scrolling="no"
                    allowfullscreen
                    title="Preview ${escapeHtml(titulo)}"
                ></iframe>
                <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                    <i class="fab fa-instagram text-white text-4xl"></i>
                </div>
            </div>
        `);
    } else {
        $('#previewContainer').addClass('hidden');
    }
}

// Resetear modal de testimonio
function resetTestimonioModal() {
    console.log('🔄 Resetear modal de testimonio');
    $('#testimonioModalTitle').text('Nuevo Testimonio');
    $('#testimonio_id').val('');
    $('#testimonio_reel').val('');
    $('#testimonio_titulo').val('');
    $('#testimonio_estado').val('1');
    $('#previewContainer').addClass('hidden');
}

// Aplicar filtros
function aplicarFiltros() {
    var estadoFiltro = $('#filtroEstado').val();
    var busqueda = $('#filtroBusqueda').val().toLowerCase();
    
    $('.testimonial-card').each(function() {
        var $card = $(this);
        var estado = $card.data('estado');
        var titulo = $card.find('h3').text().toLowerCase();
        var mostrar = true;
        
        if(estadoFiltro !== 'todos' && estado != estadoFiltro) {
            mostrar = false;
        }
        
        if(busqueda && !titulo.includes(busqueda)) {
            mostrar = false;
        }
        
        if(mostrar) {
            $card.show();
        } else {
            $card.hide();
        }
    });
}

// Form handlers
$('#testimonioForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = {
        id: $('#testimonio_id').val(),
        reel: $('#testimonio_reel').val().trim(),
        titulo: $('#testimonio_titulo').val().trim(),
        estado: $('#testimonio_estado').val()
    };
    
    console.log('📤 Enviando datos testimonio:', formData);
    
    // Validaciones básicas
    if(!formData.reel) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El ID del reel es requerido'
        });
        return;
    }
    
    if(!formData.titulo) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El título es requerido'
        });
        return;
    }
    
    // Validar formato del ID (solo alfanumérico, guiones y guiones bajos)
    if(!/^[a-zA-Z0-9_-]+$/.test(formData.reel)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El ID del reel solo puede contener letras, números, guiones y guiones bajos'
        });
        return;
    }
    
    $.ajax({
        url: '/mi/models/guardarInstagram',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta:', response);
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Testimonio guardado',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                closeModal('testimonioModal');
                cargarTestimonios();
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
                text: 'Error al guardar testimonio: ' + error
            });
        }
    });
});

// Event listeners para filtros
$('#filtroEstado').on('change', function() {
    aplicarFiltros();
});

$('#filtroBusqueda').on('keyup', function() {
    aplicarFiltros();
});

// Auto preview al escribir el ID del reel
$('#testimonio_reel').on('input', function() {
    var reel = $(this).val().trim();
    var titulo = $('#testimonio_titulo').val().trim();
    if(reel && titulo) {
        mostrarPreview(reel, titulo);
    } else {
        $('#previewContainer').addClass('hidden');
    }
});

$('#testimonio_titulo').on('input', function() {
    var titulo = $(this).val().trim();
    var reel = $('#testimonio_reel').val().trim();
    if(reel && titulo) {
        mostrarPreview(reel, titulo);
    } else {
        $('#previewContainer').addClass('hidden');
    }
});

// Inicializar
$(document).ready(function() {
    console.log('📌 Documento listo, iniciando carga de testimonios...');
    cargarTestimonios();
});