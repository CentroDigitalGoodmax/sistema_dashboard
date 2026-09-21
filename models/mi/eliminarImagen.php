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
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }

    // Obtener la imagen para eliminar el archivo
    $query = "SELECT imagen_url FROM galeria WHERE id = ?";
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $imagen = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Eliminar del servidor (si existe el archivo)
    if ($imagen && !empty($imagen['imagen_url'])) {
        $filepath = $_SERVER['DOCUMENT_ROOT'] . $imagen['imagen_url'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    // Eliminar del BD
    $query = "DELETE FROM galeria WHERE id = ?";
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Imagen eliminada correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al eliminar: ' . mysqli_stmt_error($stmt)];
    }
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>
