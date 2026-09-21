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
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : 0;

    if (empty($titulo)) {
        echo json_encode(['success' => false, 'message' => 'El título es requerido']);
        exit;
    }

    if ($categoria_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Selecciona una categoría']);
        exit;
    }

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != 0) {
        echo json_encode(['success' => false, 'message' => 'Error en la carga del archivo']);
        exit;
    }

    $file = $_FILES['imagen'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB máximo
        echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 5MB)']);
        exit;
    }

    // Crear directorio de subidas si no existe
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/galeria';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generar nombre único para el archivo
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '.' . $ext;
    $filepath = $upload_dir . '/' . $filename;
    $url = '/assets/galeria/' . $filename;

    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen']);
        exit;
    }

    // Guardar en BD
    $query = "INSERT INTO galeria (titulo, descripcion, categoria_id, imagen_url, fecha_publicacion) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $titulo, $descripcion, $categoria_id, $url);

    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Imagen subida correctamente', 'url' => $url];
    } else {
        // Eliminar archivo si falla la BD
        unlink($filepath);
        $response = ['success' => false, 'message' => 'Error al guardar en BD: ' . mysqli_stmt_error($stmt)];
    }
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>
