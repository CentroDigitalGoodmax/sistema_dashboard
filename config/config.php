<?php
error_reporting(0);
date_default_timezone_set('America/Bogota');

/* ============================================================
 *  HELPERS
 * ============================================================ */

function app_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }

    return (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
}

function app_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    $csp = "default-src 'self'; " .
        "base-uri 'self'; " .
        "form-action 'self'; " .
        "object-src 'none'; " .
        "frame-ancestors 'none'; " .
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://code.jquery.com; " .
        "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.tailwindcss.com; " .
        "img-src 'self' data: https:; " .
        "font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:; " .
        "connect-src 'self' https:;";

    header('Content-Security-Policy: ' . $csp);

    if (app_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function app_destroy_session(): void
{
    $_SESSION = [];

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

    session_destroy();
}

/**
 * Valida que un redirect sea una ruta interna segura.
 * Solo permite rutas que empiecen con /mi/ y no contengan "//" ni saltos de línea.
 */
function app_safe_redirect(?string $url, string $fallback = '/mi/inicio'): string
{
    if (!is_string($url) || $url === '') {
        return $fallback;
    }

    if (strpos($url, '/mi/') !== 0) {
        return $fallback;
    }

    if (strpos($url, '//') !== false
        || strpos($url, "\n") !== false
        || strpos($url, "\r") !== false
        || strpos($url, '\\') !== false) {
        return $fallback;
    }

    return $url;
}

/* ============================================================
 *  HEADERS DE SEGURIDAD
 * ============================================================ */

app_security_headers();

/* ============================================================
 *  SESIÓN
 * ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_name('argenmed_session');
    session_set_cookie_params([
        'lifetime' => 1800,
        'path'     => '/',
        'secure'   => app_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ============================================================
 *  VALIDACIÓN DE SESIÓN ACTIVA
 * ============================================================ */

if (!empty($_SESSION['id'])) {

    // 1) User-Agent debe coincidir (anti robo de cookie)
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (empty($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $userAgent) {
        app_destroy_session();
        header('Location: /acceso');
        exit();
    }

    // 2) Expiración por inactividad (30 minutos)
    if (!isset($_SESSION['last_activity'])
        || (time() - (int) $_SESSION['last_activity']) > 1800) {
        app_destroy_session();
        header('Location: /acceso');
        exit();
    }

    // Renovar marca de actividad
    $_SESSION['last_activity'] = time();
}

/* ============================================================
 *  ENRUTAMIENTO / PROTECCIÓN
 * ============================================================ */

$requestUri   = $_SERVER['REQUEST_URI'] ?? '/';
$tieneSesion  = !empty($_SESSION['id']);
$esRutaMi     = strpos($requestUri, '/mi/') === 0;
$esRutaAcceso = $requestUri === '/acceso' || strpos($requestUri, '/acceso') === 0;
$esRaiz       = $requestUri === '/' || $requestUri === '/index.php';

if ($esRutaMi) {
    // Ruta privada: requiere sesión
    if (!$tieneSesion) {
        // Guardamos a dónde quería ir (para redirigir después del login)
        $_SESSION['redirect_after_login'] = $requestUri;
        header('Location: /acceso');
        exit();
    }

} elseif ($esRutaAcceso) {
    // Si ya está logueado y entra a /acceso, lo mandamos al inicio
    if ($tieneSesion) {
        header('Location: /mi/inicio');
        exit();
    }

} elseif ($esRaiz) {
    // Raíz siempre va a /acceso
    header('Location: /acceso');
    exit();

} else {
    // Cualquier otra ruta: si tiene sesión → /mi/inicio, si no → /acceso
    if ($tieneSesion) {
        header('Location: /mi/inicio');
        exit();
    }

    header('Location: /acceso');
    exit();
}