<?php
  http_response_code(503);
  header('Retry-After: 3600'); // sugerir reintentar en 1 hora
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>503 - Servicio no disponible | Argen Medical</title>
    <meta name="description" content="Servicio temporalmente no disponible">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .error-code {
            font-size: 8rem;
            line-height: 1;
            font-weight: 800;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .floating { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8 transform transition-all duration-300 hover:shadow-3xl text-center">

            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mb-4 shadow-inner floating">
                <i class="fas fa-tools text-3xl text-purple-600"></i>
            </div>

            <div class="error-code">503</div>

            <h2 class="text-2xl font-bold text-gray-800 mt-2">Servicio no disponible</h2>
            <p class="text-gray-500 text-sm mt-2 mb-6">
                Estamos realizando tareas de mantenimiento. Por favor, vuelve pronto.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/"
                   class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Ir al inicio
                </a>
                <button onclick="location.reload()"
                        class="inline-flex items-center justify-center bg-gray-100 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-redo mr-2"></i>
                    Reintentar
                </button>
            </div>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-white text-gray-400">Argen Medical</span>
                </div>
            </div>

            <div class="text-center text-xs text-gray-400 space-y-1">
                <i class="fas fa-shield-alt mr-1"></i>
                <span>Sistema protegido</span>
            </div>
        </div>

        <p class="text-center text-white/80 text-xs mt-6">
            © <?php echo date('Y'); ?> Argen Medical -
            <a href="https://goodmaxdigital.com" target="_blank" class="text-blue-500 hover:text-blue-700">Centro Digital GoodMax</a>
            Todos los derechos reservados.
        </p>
    </div>
</body>
</html>