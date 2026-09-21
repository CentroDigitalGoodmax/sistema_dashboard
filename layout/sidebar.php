<!-- sidebar.php - Componente de Sidebar Responsive en Modo Oscuro -->
<aside class="sidebar fixed lg:static inset-y-0 left-0 z-50 w-72 bg-gray-900 shadow-2xl flex-shrink-0 transform transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0" id="sidebar">
    <!-- Logo y título -->
    <div class="p-4 border-b border-gray-800 bg-gradient-to-r from-gray-900 to-gray-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-600 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-600/30">
                    <i class="fas fa-camera-retro text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">ArgenMedical</h2>
                    <p class="text-xs text-gray-400 font-medium">Panel de Control</p>
                </div>
            </div>
            <!-- Botón cerrar sidebar (solo móvil) -->
            <button class="lg:hidden text-gray-400 hover:text-white transition-colors" id="closeSidebarBtn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- Navegación -->
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto" style="max-height: calc(100vh - 200px);">
        <?php
        // Obtener la URL actual para resaltar el enlace activo
        $current_url = $_SERVER['REQUEST_URI'] ?? '/';
        $current_url = strtok($current_url, '?');
        
        // Definir los items del menú
        $menu_items = [
            ['url' => '/mi/inicio', 'icon' => 'fas fa-th-large', 'label' => 'Inicio', 'active_exact' => true],
            ['url' => '/mi/visados', 'icon' => 'fas fa-passport', 'label' => 'Visados'],
            ['url' => '/mi/testimonios', 'icon' => 'fas fa-quote-right', 'label' => 'Testimonios'],
            ['url' => '/mi/metodo360', 'icon' => 'fas fa-sync-alt', 'label' => 'Método 360'],
            ['url' => '/mi/resoluciones', 'icon' => 'fas fa-file-alt', 'label' => 'Resoluciones'],
            ['url' => '/mi/galeria', 'icon' => 'fas fa-images', 'label' => 'Galería'],
            ['url' => '/mi/podcast', 'icon' => 'fab fa-youtube', 'label' => 'Podcast'],
            ['url' => '/mi/usuarios', 'icon' => 'fas fa-users-cog', 'label' => 'Usuarios'],
        ];
        
        
        foreach ($menu_items as $item):
            $is_active = false;
            if (isset($item['active_exact']) && $item['active_exact']) {
                $is_active = ($current_url == $item['url']);
            } else {
                $is_active = (strpos($current_url, $item['url']) === 0 && $item['url'] != '/');
            }
            
            $active_class = $is_active 
                ? 'bg-gradient-to-r from-amber-600 to-amber-600 text-white shadow-lg shadow-amber-600/20' 
                : 'text-gray-300 hover:bg-gray-800 hover:text-white';
        ?>
        <a href="<?php echo $item['url']; ?>" 
           class="sidebar-link flex items-center px-4 py-3 rounded-xl transition-all duration-200 group <?php echo $active_class; ?>">
            <i class="<?php echo $item['icon']; ?> w-5 text-center mr-3 text-current transition-transform group-hover:scale-110"></i>
            <span class="flex-1 font-medium text-sm"><?php echo $item['label']; ?></span>
            <?php if($is_active): ?>
                <span class="w-1.5 h-6 bg-white rounded-full shadow-sm"></span>
            <?php else: ?>
                <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-all duration-200 text-gray-600"></i>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
        
        <!-- Espaciador -->
        <div class="flex-1"></div>
    </nav>
    
    <!-- Perfil de usuario (parte inferior) -->
    <?php
        $perfilNombre = $_SESSION['nombre'] ?? 'Usuario';
        $perfilEmail  = $_SESSION['email']  ?? 'usuario@email.com';
        $perfilAvatar = $_SESSION['avatar'] ?? '';

        // Si no tiene avatar, usamos ui-avatars como respaldo
        if (empty($perfilAvatar)) {
            $perfilAvatar = 'https://ui-avatars.com/api/?background=d97706&color=fff&bold=true&size=80&name='
                        . urlencode($perfilNombre);
        }
    ?>
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-800/50 backdrop-blur-sm border-t border-gray-800">
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-800 transition-all duration-200 cursor-pointer group">
            <img src="<?= htmlspecialchars($perfilAvatar, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($perfilNombre, ENT_QUOTES, 'UTF-8') ?>"
                class="w-12 h-12 rounded-full ring-2 ring-amber-600/50 group-hover:ring-amber-400 transition-all duration-300 shadow-lg shadow-amber-600/20 object-cover"
                onerror="this.src='https://ui-avatars.com/api/?background=d97706&color=fff&bold=true&size=80&name=<?= urlencode($perfilNombre) ?>'">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($perfilNombre, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($perfilEmail, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="/auth/cerrar" class="p-2 hover:bg-red-600/10 rounded-lg transition-colors group/btn" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt text-gray-400 group-hover/btn:text-red-400 transition-colors"></i>
                </a>
            </div>
        </div>
    </div>
</aside>

<!-- Overlay para móvil -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden opacity-0 invisible transition-all duration-300"></div>

<!-- Script para funcionalidad responsive mejorado -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        // Función para abrir sidebar
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('opacity-0', 'invisible');
            overlay.classList.add('opacity-100', 'visible');
            document.body.style.overflow = 'hidden';
        }
        
        // Función para cerrar sidebar
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('opacity-0', 'invisible');
            overlay.classList.remove('opacity-100', 'visible');
            document.body.style.overflow = '';
        }
        
        // Buscar o crear botón de menú
        let menuBtn = document.querySelector('#menuToggleBtn');
        if (!menuBtn) {
            const headerLeft = document.querySelector('header .flex.items-center');
            if (headerLeft) {
                menuBtn = document.createElement('button');
                menuBtn.id = 'menuToggleBtn';
                menuBtn.className = 'lg:hidden mr-3 p-2 rounded-lg hover:bg-gray-800 transition-colors text-gray-400 hover:text-amber-400';
                menuBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
                menuBtn.setAttribute('aria-label', 'Abrir menú');
                headerLeft.insertBefore(menuBtn, headerLeft.firstChild);
            }
        }
        
        // Event listeners
        const menuToggleBtn = document.getElementById('menuToggleBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        
        if (menuToggleBtn) {
            menuToggleBtn.addEventListener('click', openSidebar);
        }
        
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', closeSidebar);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        
        // Cerrar al redimensionar a desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.add('opacity-0', 'invisible');
                overlay.classList.remove('opacity-100', 'visible');
                document.body.style.overflow = '';
            } else {
                if (!sidebar.classList.contains('translate-x-0')) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
        
        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && 
                sidebar.classList.contains('translate-x-0') && 
                window.innerWidth < 1024) {
                closeSidebar();
            }
        });
        
        // Prevenir scroll cuando el sidebar está abierto en móvil
        document.addEventListener('touchmove', function(e) {
            if (window.innerWidth < 1024 && 
                sidebar.classList.contains('translate-x-0')) {
                e.preventDefault();
            }
        }, { passive: false });
        
        // Cerrar automáticamente al hacer clic en un enlace en móvil
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth < 1024 && 
                    !this.querySelector('.fa-chevron-right')) {
                    setTimeout(closeSidebar, 300);
                }
            });
        });
    });
</script>

<style>
    /* Estilos para modo oscuro */
    .sidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.5);
        border-right: 1px solid rgba(55, 65, 81, 0.5);
    }
    
    .sidebar-link {
        position: relative;
        overflow: hidden;
        font-weight: 600;
    }
    
    .sidebar-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #8B5CF6, #3B82F6);
        transform: scaleY(0);
        transition: transform 0.2s ease;
        transform-origin: center;
        border-radius: 0 3px 3px 0;
    }
    
    .sidebar-link:hover::before {
        transform: scaleY(1);
    }
    
    .sidebar-link.active::before {
        transform: scaleY(1);
    }
    
    /* Scrollbar personalizada en modo oscuro */
    .sidebar nav::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar nav::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .sidebar nav::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 10px;
    }
    
    .sidebar nav::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
    
    /* Efecto hover en perfil */
    .group i {
        transition: transform 0.3s ease;
    }
    
    .group:hover i {
        transform: rotate(180deg);
    }
    
    /* Efecto de brillo en hover */
    .sidebar-link:hover {
        transform: translateX(2px);
    }
    
    /* Responsive mejorado */
    @media (max-width: 1023px) {
        .sidebar {
            width: 85%;
            max-width: 320px;
            box-shadow: 4px 0 40px rgba(0, 0, 0, 0.8);
        }
    }
    
    @media (min-width: 1024px) {
        .sidebar {
            width: 280px;
        }
    }
    
    /* Animación de entrada del overlay */
    #sidebarOverlay {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    /* Glow effect para el logo */
    .sidebar .w-12.h-12 {
        animation: glow 2s ease-in-out infinite alternate;
    }
    
    @keyframes glow {
        from {
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.2);
        }
        to {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.4);
        }
    }
</style>