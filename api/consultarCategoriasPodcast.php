<?php
// consultarCategoriasPodcast.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('../config/conex.php');
include('../config/openssl_decrypt_pass_cs.php');
include('../config/encriptar.php');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$query = "SELECT 
            id, 
            nombre, 
            slug, 
            descripcion, 
            color, 
            orden, 
            activo, 
            created_at 
          FROM categoria_podcast 
          WHERE activo = 1 
          ORDER BY orden ASC, id ASC";

$result = mysqli_query($conex, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$categorias = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = encriptar_datos($row['id'], $clave_secreta, $iv);
    $row['orden'] = (int)$row['orden'];
    $row['activo'] = (int)$row['activo'];
    $row['color'] = $row['color'] ?? '#6366f1';
    
    $categorias[] = $row;
}

http_response_code(200);
echo json_encode($categorias, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>