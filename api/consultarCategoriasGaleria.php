<?php
// Configurar CORS para permitir peticiones desde cualquier origen
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

// Corregir nombre de tabla: 'categoria_galeria' en lugar de 'categoria_galeria'
// y ordenar por el campo 'orden' si existe
$query = "SELECT 
            id, 
            nombre, 
            slug, 
            descripcion, 
            icono, 
            color, 
            orden, 
            activo, 
            created_at 
          FROM categoria_galeria 
          WHERE activo = '1' 
          ORDER BY CAST(orden AS UNSIGNED) ASC, id ASC";

$result = mysqli_query($conex, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$categorias = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convertir tipos de datos
    $row['id'] = encriptar_datos($row['id'], $clave_secreta, $iv);
    $row['activo'] = (int)$row['activo'];
    $row['orden'] = isset($row['orden']) ? (int)$row['orden'] : 0;
    
    // Asegurar que los campos opcionales tengan valores por defecto
    $row['slug'] = $row['slug'] ?? '';
    $row['icono'] = $row['icono'] ?? 'Folder';
    $row['color'] = $row['color'] ?? '#6b7280';
    $row['descripcion'] = $row['descripcion'] ?? '';
    
    $categorias[] = $row;
}

// Devolver JSON con código de éxito
http_response_code(200);
echo json_encode($categorias, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>