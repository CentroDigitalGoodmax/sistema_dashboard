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
    function generateSlug($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $icono = isset($_POST['icono']) ? trim($_POST['icono']) : 'fas fa-folder';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#6366F1';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : generateSlug($nombre);
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;
    $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    $created_at = date('Y-m-d H:i:s');

    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit;
    }

    if ($id > 0) {
        // Actualizar
        $query = "UPDATE categoria_galeria SET nombre = ?, slug = ?, descripcion = ?, icono = ?, color = ?, orden = ?, activo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "sssssiii", $nombre, $slug, $descripcion, $icono, $color, $orden, $activo, $id);
    } else {
        // Insertar
        $query = "INSERT INTO categoria_galeria (nombre, slug, descripcion, icono, color, orden, activo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "sssssiis", $nombre, $slug, $descripcion, $icono, $color, $orden, $activo, $created_at);
    }

    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Categoría guardada correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al guardar: ' . mysqli_stmt_error($stmt)];
    }
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>
