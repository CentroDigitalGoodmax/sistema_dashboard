<?php
// Configurar CORS para permitir peticiones desde cualquier origen (o específicos)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Manejar solicitud preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexión a BD
include('../config/conex.php');
include('../config/openssl_decrypt_pass_cs.php');
include('../config/encriptar.php');
// Verificar conexión
if (!$conex) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$query = "SELECT id, img FROM resoluciones WHERE estado = 1 ORDER BY id DESC";
$result = mysqli_query($conex, $query);


if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$resoluciones = [];
while ($row = mysqli_fetch_assoc($result)) {

    $row['id'] = encriptar_datos($row['id'], $clave_secreta, $iv);
    
    // Asegurar URLs completas si es necesario
    if (!empty($row['img']) && strpos($row['img'], 'http') !== 0) {
        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $row['img'] = $baseUrl . '/' . ltrim($row['img'], '/');
    }
    
    if (!empty($row['imagen_thumbnail']) && strpos($row['imagen_thumbnail'], 'http') !== 0) {
        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $row['imagen_thumbnail'] = $baseUrl . '/' . ltrim($row['imagen_thumbnail'], '/');
    }
    
    $resoluciones[] = $row;
}

// Devolver JSON
$response = [
    'data' => $resoluciones
];

// Devolver JSON con código de éxito
http_response_code(200);
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>