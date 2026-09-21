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

// Consulta para obtener solo elementos activos
$query = "SELECT 
            id, 
            titulo, 
            descripcion, 
            imagen_url, 
            imagen_thumbnail, 
            categoria_id, 
            usuario_id, 
            visitas, 
            likes, 
            activo, 
            destacado, 
            fecha_publicacion, 
            created_at, 
            updated_at 
          FROM galeria 
          WHERE activo = '1' 
          ORDER BY destacado DESC, fecha_publicacion DESC, id DESC";

$result = mysqli_query($conex, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$imagenes = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convertir valores numéricos que vienen como string
    $row['id'] = encriptar_datos($row['id'], $clave_secreta, $iv);
    $row['visitas'] = (int)$row['visitas'];
    $row['likes'] = (int)$row['likes'];
    $row['activo'] = (int)$row['activo'];
    $row['destacado'] = (int)$row['destacado'];
    
    // Asegurar URLs completas si es necesario
    if (!empty($row['imagen_url']) && strpos($row['imagen_url'], 'http') !== 0) {
        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $row['imagen_url'] = $baseUrl . '/' . ltrim($row['imagen_url'], '/');
    }
    
    if (!empty($row['imagen_thumbnail']) && strpos($row['imagen_thumbnail'], 'http') !== 0) {
        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $row['imagen_thumbnail'] = $baseUrl . '/' . ltrim($row['imagen_thumbnail'], '/');
    }
    
    $imagenes[] = $row;
}

// Devolver JSON con código de éxito
http_response_code(200);
echo json_encode($imagenes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>