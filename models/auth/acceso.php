<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/openssl_decrypt_pass_cs.php';
require_once __DIR__ . '/../../config/desencriptar.php';

date_default_timezone_set('America/Bogota');

/* ============================================================
 *  HELPERS (mismos que en config.php, por consistencia)
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
 *  HEADERS
 * ============================================================ */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

/* ============================================================
 *  VALIDACIONES BÁSICAS
 * ============================================================ */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['code' => 400, 'message' => 'Método no permitido']);
    exit;
}

if (empty($_POST['correo']) || empty($_POST['pass'])) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'Correo y contraseña son requeridos']);
    exit;
}

$correo   = strtolower(trim((string) $_POST['correo']));
$pass     = trim((string) $_POST['pass']);
$remember = isset($_POST['remember']) ? (int) $_POST['remember'] : 0;

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'Ingrese un correo válido']);
    exit;
}

/* ============================================================
 *  BLOQUEO POR INTENTOS FALLIDOS
 * ============================================================ */

$lockUntil = $_SESSION['login_lock_until'] ?? 0;
if ($lockUntil > time()) {
    $remaining = max(1, (int) ceil(($lockUntil - time()) / 60));
    http_response_code(429);
    echo json_encode([
        'code'    => 429,
        'message' => 'Demasiados intentos. Intente nuevamente en ' . $remaining . ' minutos.',
    ]);
    exit;
}

/* ============================================================
 *  AUTENTICACIÓN
 * ============================================================ */

try {
    $sql  = "SELECT id, nombre, email, password, activo, avatar, ultimo_acceso
             FROM usuarios
             WHERE email = ?";
    $stmt = $conex->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta');
    }

    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $num       = $resultado->num_rows;

    if ($num > 0) {
        $row = $resultado->fetch_assoc();

        if ((int) $row['activo'] !== 1) {
            http_response_code(403);
            echo json_encode([
                'code'    => 403,
                'message' => 'Usuario inactivo. Contacte al administrador.',
            ]);
            $stmt->close();
            exit;
        }

        $clave_acceso = desencriptar_datos($row['password'], $clave_secreta);

        if ($clave_acceso === $pass) {

            /* ---------- LOGIN EXITOSO ---------- */
            session_regenerate_id(true);

            $_SESSION['id']             = $row['id'];
            $_SESSION['nombre']         = $row['nombre'];
            $_SESSION['email']          = $row['email'];
            $_SESSION['avatar']         = $row['avatar'];
            $_SESSION['login_time']     = time();
            $_SESSION['last_activity']  = time();
            $_SESSION['user_agent']     = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['login_lock_until']);

            // Actualizar último acceso
            $fecha_actual = date('Y-m-d H:i:s');
            $update_sql   = 'UPDATE usuarios SET ultimo_acceso = ? WHERE id = ?';
            $update_stmt  = $conex->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param('si', $fecha_actual, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // Determinar a dónde redirigir (deep link o /mi/inicio)
            $redirect = '/mi/inicio';
            if (!empty($_SESSION['redirect_after_login'])) {
                $redirect = app_safe_redirect(
                    $_SESSION['redirect_after_login'],
                    '/mi/inicio'
                );
                unset($_SESSION['redirect_after_login']);
            }

            http_response_code(200);
            echo json_encode([
                'code'     => 200,
                'message'  => 'Login exitoso',
                'redirect' => $redirect,
                'user'     => [
                    'nombre' => $row['nombre'],
                    'email'  => $row['email'],
                ],
            ]);

        } else {

            /* ---------- CONTRASEÑA INCORRECTA ---------- */
            $attempts = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['login_attempts'] = $attempts;

            if ($attempts >= 5) {
                $_SESSION['login_lock_until'] = time() + 300;
                $_SESSION['login_attempts']   = 0;
            }

            http_response_code(401);
            echo json_encode(['code' => 201, 'message' => 'Contraseña incorrecta']);
        }

    } else {

        /* ---------- CORREO NO REGISTRADO ---------- */
        $attempts = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['login_attempts'] = $attempts;

        if ($attempts >= 5) {
            $_SESSION['login_lock_until'] = time() + 300;
            $_SESSION['login_attempts']   = 0;
        }

        http_response_code(401);
        echo json_encode(['code' => 202, 'message' => 'El correo no está registrado']);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['code' => 500, 'message' => 'Error interno del servidor']);
}

$conex->close();