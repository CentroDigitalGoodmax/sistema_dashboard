<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';

$idUsuario = requireAuth();

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a BD']);
    exit;
}

$query = "SELECT id, nombre, email, avatar, activo, ultimo_acceso, created_at
          FROM usuarios
          ORDER BY id DESC";

$result = $conex->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . $conex->error]);
    exit;
}

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);

$conex->close();