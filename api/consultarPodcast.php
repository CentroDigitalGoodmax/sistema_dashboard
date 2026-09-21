<?php
// consultarPodcasts.php
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

// Obtener parámetros de filtrado
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
$destacados = isset($_GET['destacados']) ? filter_var($_GET['destacados'], FILTER_VALIDATE_BOOLEAN) : false;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conex, $_GET['search']) : null;

// Construir consulta base
$query = "SELECT 
            p.id, 
            p.titulo, 
            p.descripcion, 
            p.youtube_link,
            p.youtube_id,
            p.categoria_id,
            p.usuario_id,
            p.thumbnail_url,
            p.visitas,
            p.likes,
            p.duracion,
            p.fecha_publicacion,
            p.activo,
            p.destacado,
            p.created_at,
            p.updated_at,
            c.nombre as categoria_nombre,
            c.color as categoria_color
          FROM podcast p
          LEFT JOIN categoria_podcast c ON p.categoria_id = c.id
          WHERE p.activo = 1";

// Aplicar filtros
if ($categoria_id) {
    $query .= " AND p.categoria_id = $categoria_id";
}

if ($destacados) {
    $query .= " AND p.destacado = 1";
}

if ($search) {
    $query .= " AND (p.titulo LIKE '%$search%' OR p.descripcion LIKE '%$search%')";
}

// Ordenar
$query .= " ORDER BY p.destacado DESC, p.fecha_publicacion DESC, p.id DESC";

// Aplicar límite
if ($limit && $limit > 0) {
    $query .= " LIMIT $limit";
}

$result = mysqli_query($conex, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conex)]);
    exit;
}

$podcasts = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convertir tipos de datos
    $row['id'] = encriptar_datos($row['id'], $clave_secreta, $iv);
    $row['categoria_id'] = (int)$row['categoria_id'];
    $row['usuario_id'] = $row['usuario_id'] ? (int)$row['usuario_id'] : null;
    $row['visitas'] = (int)$row['visitas'];
    $row['likes'] = (int)$row['likes'];
    $row['activo'] = (int)$row['activo'];
    $row['destacado'] = (int)$row['destacado'];
    
    // Asegurar valores por defecto
    $row['duracion'] = $row['duracion'] ?? '00:00';
    $row['descripcion'] = $row['descripcion'] ?? '';
    $row['youtube_id'] = $row['youtube_id'] ?? null;
    
    // Construir URL de YouTube si solo tenemos el ID
    if (!empty($row['youtube_link'])) {
        // Si ya es una URL completa, mantenerla
        if (strpos($row['youtube_link'], 'http') !== 0) {
            $row['youtube_link'] = 'https://www.youtube.com/watch?v=' . $row['youtube_link'];
        }
    } elseif (!empty($row['youtube_id'])) {
        $row['youtube_link'] = 'https://www.youtube.com/watch?v=' . $row['youtube_id'];
    }
    
    // Construir URL completa para thumbnail
    $baseUrl = rtrim((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/');
    
    if (!empty($row['thumbnail_url'])) {
        if (strpos($row['thumbnail_url'], 'http') !== 0) {
            $row['thumbnail_url'] = $baseUrl . '/' . ltrim($row['thumbnail_url'], '/');
        }
    } else {
        // Usar thumbnail de YouTube si no hay thumbnail personalizado
        if (!empty($row['youtube_id'])) {
            $row['thumbnail_url'] = 'https://img.youtube.com/vi/' . $row['youtube_id'] . '/maxresdefault.jpg';
        } else {
            $row['thumbnail_url'] = $baseUrl . '/assets/images/podcast-placeholder.jpg';
        }
    }
    
    // Formatear fecha
    $row['fecha_formateada'] = date('d/m/Y', strtotime($row['fecha_publicacion']));
    
    $podcasts[] = $row;
}

// Devolver JSON
$response = [
    'total' => count($podcasts),
    'data' => $podcasts
];

http_response_code(200);
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>