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
    
    $query = "DELETE FROM instagram WHERE id = ?";
    $stmt = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Testimonio eliminado correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al eliminar: ' . mysqli_stmt_error($stmt)];
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>