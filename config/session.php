<?php
// config/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_name('argenmed_session');
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                 || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) 
                     && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}