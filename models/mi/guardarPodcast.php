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

function extractYouTubeID($url) {
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
        '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

try {
    $id = isset($_POST['id']) && $_POST['id'] != '' ? intval($_POST['id']) : 0;
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $youtube_link = isset($_POST['youtube_link']) ? trim($_POST['youtube_link']) : '';
    $categoria_id = isset($_POST['categoria_id']) && $_POST['categoria_id'] != '' ? intval($_POST['categoria_id']) : null;
    $usuario_id = isset($_POST['usuario_id']) && $_POST['usuario_id'] != '' ? intval($_POST['usuario_id']) : 1; // Valor por defecto 1
    $duracion = isset($_POST['duracion']) && !empty($_POST['duracion']) ? trim($_POST['duracion']) : null;
    $destacado = isset($_POST['destacado']) ? intval($_POST['destacado']) : 0;
    $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    $visitas = isset($_POST['visitas']) ? intval($_POST['visitas']) : 0;
    $likes = isset($_POST['likes']) ? intval($_POST['likes']) : 0;
    $fecha_publicacion = isset($_POST['fecha_publicacion']) && !empty($_POST['fecha_publicacion']) ? $_POST['fecha_publicacion'] : date('Y-m-d');

    // Validaciones
    if (empty($titulo)) {
        echo json_encode(['success' => false, 'message' => 'El título es requerido']);
        exit;
    }

    if (empty($youtube_link)) {
        echo json_encode(['success' => false, 'message' => 'El link de YouTube es requerido']);
        exit;
    }

    // Validar y procesar YouTube link
    $youtube_id = extractYouTubeID($youtube_link);
    if (!$youtube_id) {
        echo json_encode(['success' => false, 'message' => 'Link de YouTube inválido']);
        exit;
    }
    
    $thumbnail_url = "https://img.youtube.com/vi/" . $youtube_id . "/maxresdefault.jpg";

    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_publicacion)) {
        echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido (use YYYY-MM-DD)']);
        exit;
    }

    if ($id > 0) {
        // ACTUALIZAR
        $query = "UPDATE podcast SET 
                    titulo = ?, 
                    descripcion = ?, 
                    youtube_link = ?, 
                    youtube_id = ?,
                    thumbnail_url = ?,
                    categoria_id = ?, 
                    usuario_id = ?,
                    duracion = ?, 
                    destacado = ?, 
                    activo = ?, 
                    visitas = ?,
                    likes = ?,
                    fecha_publicacion = ?
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "sssssiisiiissi", 
            $titulo, 
            $descripcion, 
            $youtube_link, 
            $youtube_id,
            $thumbnail_url,
            $categoria_id, 
            $usuario_id,
            $duracion, 
            $destacado, 
            $activo, 
            $visitas,
            $likes,
            $fecha_publicacion, 
            $id
        );
    } else {
        // INSERTAR
        $query = "INSERT INTO podcast (
                    titulo, 
                    descripcion, 
                    youtube_link, 
                    youtube_id,
                    thumbnail_url,
                    categoria_id, 
                    usuario_id,
                    duracion, 
                    destacado, 
                    activo, 
                    visitas,
                    likes,
                    fecha_publicacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "sssssiisiiiss", 
            $titulo, 
            $descripcion, 
            $youtube_link, 
            $youtube_id,
            $thumbnail_url,
            $categoria_id, 
            $usuario_id,
            $duracion, 
            $destacado, 
            $activo, 
            $visitas,
            $likes,
            $fecha_publicacion
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Post guardado correctamente'];
        if ($id == 0) {
            $response['id'] = mysqli_insert_id($conex);
        }
    } else {
        $response = ['success' => false, 'message' => 'Error al guardar: ' . mysqli_stmt_error($stmt)];
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>