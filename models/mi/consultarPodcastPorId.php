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

try {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }
    
    $query = "SELECT 
                id, 
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
                fecha_publicacion,
                created_at, 
                updated_at 
              FROM podcast 
              WHERE id = ?";
    
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);
    
    if ($post) {
        echo json_encode($post);
    } else {
        echo json_encode(['error' => 'Post no encontrado']);
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>