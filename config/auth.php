<?php
// config/auth.php
require_once __DIR__ . '/session.php';

function requireAuth(): int {
    if (empty($_SESSION['id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['code' => 401, 'message' => 'No autorizado']);
        exit;
    }
    return (int) $_SESSION['id'];
}

function isLoggedIn(): bool {
    return !empty($_SESSION['id']);
}