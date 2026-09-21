console.log('acceso.js cargado correctamente');

$(document).ready(function() {
    // Toggle de contraseña mejorado
    const passwordField = $('input[name="password"]');
    const toggleBtn = $('<button type="button" class="absolute right-3 top-9 text-gray-400 hover:text-indigo-500 transition"><i class="fas fa-eye"></i></button>');
    
    passwordField.closest('.mb-4').css('position', 'relative').append(toggleBtn);
    
    toggleBtn.on('click', function() {
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).html(type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>');
    });
    
    // Manejo del formulario con AJAX
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        // Limpiar alertas anteriores
        $('.alert').remove();
        
        const email = $('input[name="email"]').val().trim();
        const password = $('input[name="password"]').val();
        const remember = $('input[name="remember"]').is(':checked') ? 1 : 0;
        
        // Validaciones básicas
        if (!email) {
            showAlert('error', 'Por favor, ingrese su correo electrónico');
            $('input[name="email"]').focus();
            return;
        }
        
        if (!password) {
            showAlert('error', 'Por favor, ingrese su contraseña');
            $('input[name="password"]').focus();
            return;
        }
        
        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showAlert('error', 'Por favor, ingrese un correo electrónico válido');
            $('input[name="email"]').focus();
            return;
        }
        
        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Validando credenciales...');
        submitBtn.prop('disabled', true);
        
        // Enviar petición AJAX
        $.ajax({
            url: '/auth/acceso',
            type: 'POST',
            data: {
                correo: email,
                pass: password,
                remember: remember
            },
            dataType: 'json',
            timeout: 30000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                switch(response.code) {
                    case 200:
                        // Login exitoso
                        showAlert('success', '¡Bienvenido ' + (response.user?.nombre || 'Usuario') + '! Redirigiendo...');
                        
                        // Guardar cookie si "Recordarme" está marcado
                        if (remember) {
                            const expiryDate = new Date();
                            expiryDate.setDate(expiryDate.getDate() + 30);
                            document.cookie = `user_email=${encodeURIComponent(email)}; path=/; expires=${expiryDate.toUTCString()}; SameSite=Lax`;
                        } else {
                            document.cookie = 'user_email=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
                        }
                        
                        // Redirigir después de 1.5 segundos
                        setTimeout(function() {
                            window.location.href = response.redirect || '/mi/inicio';
                        }, 1500);
                        break;
                        
                    case 201:
                        showAlert('error', '❌ Contraseña incorrecta');
                        $('input[name="password"]').val('').focus();
                        break;
                        
                    case 202:
                        showAlert('error', '❌ El correo electrónico no está registrado');
                        $('input[name="email"]').select();
                        break;
                        
                    case 403:
                        showAlert('error', '⚠️ ' + response.message);
                        break;
                        
                    default:
                        showAlert('error', response.message || 'Error al procesar la solicitud');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', {status, error, response: xhr.responseText});
                
                let errorMsg = 'Error de conexión. ';
                if (status === 'timeout') {
                    errorMsg += 'El servidor no responde. Intente nuevamente.';
                } else if (status === 'parsererror') {
                    errorMsg += 'Error en la respuesta del servidor.';
                } else {
                    errorMsg += 'Por favor, verifique su conexión e intente nuevamente.';
                }
                
                showAlert('error', errorMsg);
            },
            complete: function() {
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Función para mostrar alertas
    function showAlert(type, message) {
        const alertClass = type === 'success' 
            ? 'bg-green-50 border-green-200 text-green-700' 
            : 'bg-red-50 border-red-200 text-red-700';
        
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert mb-4 p-3 ${alertClass} border rounded-lg animate-fade-in shadow-sm">
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas ${icon}"></i>
                    <span class="flex-1">${escapeHtml(message)}</span>
                    <button type="button" class="close-alert text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('.alert').remove();
        $('#loginForm').before(alertHtml);
        
        $('.close-alert').on('click', function() {
            $(this).closest('.alert').fadeOut('fast', function() {
                $(this).remove();
            });
        });
        
        if (type === 'success') {
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }
    }
    
    // Función para escapar HTML y prevenir XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Agregar animaciones CSS
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes fadeIn {
                from { 
                    opacity: 0; 
                    transform: translateY(-10px); 
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0); 
                }
            }
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
            input:focus {
                transform: translateY(-1px);
                transition: all 0.2s ease;
            }
        `)
        .appendTo('head');
    
    // Auto-focus en el campo de email si está vacío
    if (!$('input[name="email"]').val()) {
        $('input[name="email"]').focus();
    }
});