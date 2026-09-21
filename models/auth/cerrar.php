<?php
require_once __DIR__ . '/../../config/auth.php';

// Vaciar variables de sesión
$_SESSION = [];

// Borrar la cookie de sesión del navegador
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]
    );
}

// Destruir la sesión en el servidor
session_destroy();

// Redirigir y detener
header('Location: /');
exit;