<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';

// requireAuth() corta con 401 si no está logueado, y ya manda Content-Type
$idUsuario = requireAuth();

header('Content-Type: application/json');


if (!$conex) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a BD']);
    exit;
}

$query = "SELECT * FROM podcast ORDER BY fecha_publicacion DESC, id DESC";
$result = mysqli_query($conex, $query);

if (!$result) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$podcast = [];
while ($row = mysqli_fetch_assoc($result)) {
    $podcast[] = $row;
}

echo json_encode($podcast);
?>