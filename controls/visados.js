console.log('visados.js cargado correctamente');

let visadosData = [];

// Cargar visados
function cargarvisados() {
    var $container = $('#visadosContainer');
    $container.html('<div class="col-span-full"><div class="loader"></div></div>');
    
    console.log('📡 Intentando cargar visados...');
    
    $.ajax({
        url: '/mi/models/consultarvisados',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('visados recibidas:', response);
            visadosData = response;
            renderizarvisados(response);
            actualizarEstadisticas(response);
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando visados:', error);
            $container.html('<div class="col-span-full text-center text-red-500 py-8">Error al cargar visados: ' + error + '</div>');
        }
    });
}

// Actualizar estadísticas
function actualizarEstadisticas(visados) {
    if(!visados || visados.length === 0) {
        $('#totalvisados').text('0');
        $('#totalActivas').text('0');
        $('#totalInactivas').text('0');
        $('#ultimaActualizacion').text('-');
        return;
    }
    
    var total = visados.length;
    var activas = 0;
    var inactivas = 0;
    var ultimaFecha = null;
    
    for(var i = 0; i < visados.length; i++) {
        if(visados[i].estado == 1) {
            activas++;
        } else {
            inactivas++;
        }
        
        // Obtener la fecha más reciente
        if(visados[i].updated_at) {
            var fecha = new Date(visados[i].updated_at);
            if(!ultimaFecha || fecha > ultimaFecha) {
                ultimaFecha = fecha;
            }
        }
    }
    
    $('#totalvisados').text(total);
    $('#totalActivas').text(activas);
    $('#totalInactivas').text(inactivas);
    
    if(ultimaFecha) {
        $('#ultimaActualizacion').text(formatFecha(ultimaFecha));
    } else {
        $('#ultimaActualizacion').text('-');
    }
}

function formatFecha(date) {
    var options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('es-ES', options);
}

// Renderizar visados
function renderizarvisados(visados) {
    var $container = $('#visadosContainer');
    
    if(!visados || visados.length === 0) {
        $container.html('<div class="col-span-full text-center text-gray-500 py-8">No hay visados disponibles</div>');
        return;
    }
    
    var html = '';
    for(var i = 0; i < visados.length; i++) {
        var res = visados[i];
        var estadoClase = res.estado == 1 ? 'estado-activo' : 'estado-inactivo';
        var estadoTexto = res.estado == 1 ? 'Activo' : 'Inactivo';
        
        var imagenUrl = res.img || '/assets/visados/default.jpg';
        var fecha = res.created_at ? new Date(res.created_at) : new Date();
        var fechaFormateada = fecha.toLocaleDateString('es-ES');
        
        html += `
            <div class="resolucion-card bg-white rounded-xl shadow-sm overflow-hidden group cursor-pointer" 
                 data-estado="${res.estado}" 
                 onclick="verVisado('${imagenUrl}', '${escapeHtml(res.persona)}', '${fechaFormateada}')">
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    <img src="${imagenUrl}" alt="${escapeHtml(res.persona)}" class="resolucion-img" onerror="this.src='/assets/visados/default.jpg'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-start p-3">
                        <span class="text-white text-xs font-medium px-3 py-1 rounded-full bg-black/50 backdrop-blur-sm border border-white/20">
                            <i class="fas fa-search mr-1"></i> Ver detalle
                        </span>
                    </div>
                    <div class="absolute top-2 right-2">
                        <span class="estado-badge ${estadoClase}">${estadoTexto}</span>
                    </div>
                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-user"></i>
                        <span>#${res.id}</span>
                    </div>
                </div>
                <div class="p-3">
                    <h3 class="font-medium text-gray-800 text-sm truncate">${escapeHtml(res.persona)}</h3>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-500">
                            <i class="far fa-calendar-alt"></i> ${fechaFormateada}
                        </span>
                    </div>
                    <div class="flex gap-2 mt-3 text-xs border-t border-gray-100 pt-3">
                        <button onclick="event.stopPropagation(); editVisado(${res.id})" class="flex-1 text-blue-500 hover:text-blue-600 hover:bg-blue-50 py-1.5 rounded transition flex items-center justify-center gap-1">
                            <i class="fas fa-edit text-xs"></i> Editar
                        </button>
                        <button onclick="event.stopPropagation(); deleteVisado(${res.id})" class="flex-1 text-red-500 hover:text-red-600 hover:bg-red-50 py-1.5 rounded transition flex items-center justify-center gap-1">
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

// Nueva función para abrir modal de resolución (CORREGIDO: nombre coincide con HTML)
function nuevoVisado() {
    console.log('🆕 Abriendo modal para nueva resolución');
    resetVisadoModal();
    $('#visadoModalTitle').text('Nuevo Visado');
    openModal('visadoModal');
}

// Función para recargar visados
function recargarvisados() {
    cargarvisados();
    Swal.fire({
        icon: 'info',
        title: 'Recargando...',
        text: 'Las visados se han actualizado',
        showConfirmButton: false,
        timer: 1500
    });
}

// Función para editar resolución
function editVisado(id) {
    console.log('✏️ Editando resolución ID:', id);
    var Visado = null;
    for(var i = 0; i < visadosData.length; i++) {
        if(visadosData[i].id == id) {
            Visado = visadosData[i];
            break;
        }
    }
    if(Visado) {
        $('#visadoModalTitle').text('Editar Visado');
        $('#visado_id').val(Visado.id);
        $('#visado_persona').val(Visado.persona);
        $('#visado_estado').val(Visado.estado);
        
        // Mostrar imagen existente
        if(Visado.img) {
            $('#imagePreview').attr('src', Visado.img).show();
            $('#imagePlaceholder').hide();
        } else {
            $('#imagePreview').hide();
            $('#imagePlaceholder').show();
        }
        
        openModal('visadoModal');
    } else {
        console.error('❌ Resolución no encontrada');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se encontró la resolución para editar'
        });
    }
}

// Función para eliminar resolución
function deleteVisado(id) {
    Swal.fire({
        title: '¿Eliminar resolución?',
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
                url: '/mi/models/eliminarVisado',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Resolución eliminada',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        cargarvisados();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Error al eliminar resolución'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar resolución'
                    });
                }
            });
        }
    });
}

// Función para ver resolución
function verVisado(imagenUrl, persona, fecha) {
    $('#verImagen').attr('src', imagenUrl);
    $('#verPersona').text(persona);
    $('#verFecha').text('Publicado: ' + fecha);
    openModal('verModal');
}

// Resetear modal de resolución (CORREGIDO: IDs en minúscula)
function resetVisadoModal() {
    console.log('🔄 Resetear modal de resolución');
    $('#visadoModalTitle').text('Nueva Resolución');
    $('#visado_id').val('');
    $('#visado_persona').val('');
    $('#visado_estado').val('1');
    $('#imagePreview').hide().attr('src', '');
    $('#imagePlaceholder').show();
    $('#visado_imagen').val('');
}

// Aplicar filtros
function aplicarFiltros() {
    var estadoFiltro = $('#filtroEstado').val();
    var busqueda = $('#filtroBusqueda').val().toLowerCase();
    
    $('.resolucion-card').each(function() {
        var $card = $(this);
        var estado = $card.data('estado');
        var persona = $card.find('h3').text().toLowerCase();
        var mostrar = true;
        
        if(estadoFiltro !== 'todos' && estado != estadoFiltro) {
            mostrar = false;
        }
        
        if(busqueda && !persona.includes(busqueda)) {
            mostrar = false;
        }
        
        if(mostrar) {
            $card.show();
        } else {
            $card.hide();
        }
    });
}

// Preview de imagen (CORREGIDO: ID en minúscula)
$('#visado_imagen').on('change', function(e) {
    var file = this.files[0];
    if(file) {
        var reader = new FileReader();
        reader.onload = function(ev) {
            $('#imagePreview').attr('src', ev.target.result).show();
            $('#imagePlaceholder').hide();
        };
        reader.readAsDataURL(file);
    } else {
        $('#imagePreview').hide();
        $('#imagePlaceholder').show();
    }
});

// Form handlers (CORREGIDO: IDs en minúscula)
$('#VisadoForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData();
    var id = $('#visado_id').val();
    var persona = $('#visado_persona').val().trim();
    var estado = $('#visado_estado').val();
    var imagen = $('#visado_imagen')[0].files[0];
    
    formData.append('id', id);
    formData.append('persona', persona);
    formData.append('estado', estado);
    
    if(imagen) {
        formData.append('imagen', imagen);
    }
    
    console.log('📤 Enviando datos resolución:', { id, persona, estado, tieneImagen: !!imagen });
    
    // Validaciones básicas
    if(!persona) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre de la persona es requerido'
        });
        return;
    }
    
    // Si es nueva y no tiene imagen, error
    if(!id && !imagen) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debes seleccionar una imagen para la nueva resolución'
        });
        return;
    }
    
    $.ajax({
        url: '/mi/models/guardarVisado',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Respuesta:', response);
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Resolución guardada',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                closeModal('visadoModal');
                cargarvisados();
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
                text: 'Error al guardar resolución: ' + error
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

// Inicializar
$(document).ready(function() {
    console.log('📌 Documento listo, iniciando carga de visados...');
    cargarvisados();
});