<?php
    include('../config/config.php'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Acceso | Argen Medical</title>
    <meta name="description" content="Sistema de acceso seguro">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-up': 'fadeUp .5s ease-out both',
                        'fade-in': 'fadeIn .3s ease-out',
                        'shake':   'shake .4s ease-in-out',
                        'float':   'float 14s ease-in-out infinite',
                        'spin-slow':'spin .7s linear infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%':   { opacity: 0, transform: 'translateY(12px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%':   { opacity: 0, transform: 'translateY(-8px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' },
                        },
                        shake: {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '25%':      { transform: 'translateX(-6px)' },
                            '75%':      { transform: 'translateX(6px)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translate(0,0) scale(1)' },
                            '50%':      { transform: 'translate(20px,-25px) scale(1.05)' },
                        },
                    },
                }
            }
        }
    </script>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>

    <style>
        /* Solo lo que Tailwind NO puede hacer con clases utilitarias */

        /* Autofill de Chrome (no hay clase para esto) */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #111827;
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
            transition: background-color 600000s 0s;
        }

        /* Fondo con gradiente radial (Tailwind no tiene radial-gradient nativo) */
        body {
            background:
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, .35), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(168, 85, 247, .35), transparent 45%),
                linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-sans flex items-center justify-center p-4 min-h-screen overflow-x-hidden relative">

    <!-- Blobs decorativos -->
    <div class="fixed rounded-full blur-3xl opacity-45 pointer-events-none z-0 animate-float
                w-[380px] h-[380px] bg-violet-400 -top-32 -left-32"></div>
    <div class="fixed rounded-full blur-3xl opacity-45 pointer-events-none z-0 animate-float
                w-[300px] h-[300px] bg-blue-400 -bottom-24 -right-24 [animation-delay:-7s]"></div>

    <div class="w-full max-w-md relative z-10 animate-fade-up">

        <!-- Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-white/40 p-8 sm:p-10">

            <!-- Encabezado -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600
                            rounded-2xl flex items-center justify-center mb-4
                            shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-user-lock text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-tight">
                    Bienvenido
                </h1>
                <p class="text-gray-500 text-sm mt-2">
                    Ingresa tus credenciales para continuar
                </p>
            </div>

            <!-- Alertas -->
            <div id="alertContainer"></div>

            <!-- Formulario -->
            <form id="loginForm" method="POST" novalidate autocomplete="on">

                <!-- Correo -->
                <div class="mb-5">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                            required
                            autocomplete="email"
                            inputmode="email"
                            spellcheck="false"
                            placeholder="usuario@ejemplo.com"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl bg-white
                                   text-gray-800 placeholder-gray-400 transition
                                   focus:outline-none focus:border-indigo-500
                                   focus:ring-4 focus:ring-indigo-500/15">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="mb-5">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl bg-white
                                   text-gray-800 placeholder-gray-400 transition
                                   focus:outline-none focus:border-indigo-500
                                   focus:ring-4 focus:ring-indigo-500/15">
                        <button type="button"
                                id="togglePass"
                                aria-label="Mostrar u ocultar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center
                                       text-gray-400 hover:text-indigo-600 transition">
                            <i id="togglePassIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember + olvidé -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer group select-none">
                        <input type="checkbox" name="remember" value="1"
                               class="w-4 h-4 text-indigo-600 rounded border-gray-300
                                      focus:ring-indigo-500 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-800 transition">
                            Recordarme
                        </span>
                    </label>
                    <a href="/recuperar-password"
                       class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline transition">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón -->
                <button id="btnSubmit"
                        type="submit"
                        class="group relative w-full overflow-hidden
                               bg-gradient-to-r from-indigo-600 to-purple-600
                               text-white font-semibold py-2.5 px-4 rounded-xl
                               hover:from-indigo-700 hover:to-purple-700
                               transition-all duration-200
                               hover:scale-[1.01] active:scale-[0.99]
                               shadow-md hover:shadow-lg
                               focus:outline-none focus:ring-4 focus:ring-indigo-300
                               disabled:opacity-70 disabled:cursor-not-allowed">
                    <!-- Brillo al hover -->
                    <span class="absolute top-0 -left-3/4 w-1/2 h-full
                                 bg-gradient-to-r from-transparent via-white/35 to-transparent
                                 -skew-x-12 transition-all duration-700
                                 group-hover:left-[125%]"></span>
                    <span id="btnContent" class="relative">
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión
                    </span>
                </button>
            </form>

            <!-- Separador -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white/80 text-gray-400 tracking-wide uppercase">
                        Acceso seguro
                    </span>
                </div>
            </div>

            <!-- Sello -->
            <div class="text-center text-xs text-gray-400 flex items-center justify-center gap-2">
                <i class="fas fa-shield-halved text-indigo-400"></i>
                <span>Tus datos están protegidos y cifrados</span>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/85 text-xs mt-6">
            © <?php echo date('Y'); ?> Argen Medical ·
            <a href="https://goodmaxdigital.com" target="_blank" rel="noopener"
               class="text-white font-medium underline-offset-2 hover:underline">
                Centro Digital GoodMax
            </a>
            · Todos los derechos reservados.
        </p>
    </div>

    <!-- Script de acceso -->
    <script src="/controls/acceso.js"></script>
</body>
</html>