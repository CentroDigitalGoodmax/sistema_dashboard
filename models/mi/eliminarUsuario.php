<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';

$idUsuario = requireAuth();   // usuario logueado

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

try {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario inválido']);
        exit;
    }

    // No permitir que el admin se elimine a sí mismo
    if ($id === (int) $idUsuario) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
        exit;
    }

    // No permitir eliminar el último usuario
    $res   = $conex->query("SELECT COUNT(*) AS total FROM usuarios");
    $total = (int) ($res->fetch_assoc()['total'] ?? 0);

    if ($total <= 1) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar el único usuario del sistema']);
        exit;
    }

    // Obtener avatar para borrar archivo local
    $stmt = $conex->prepare("SELECT avatar FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Eliminar registro
    $stmt = $conex->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Borrar avatar solo si es local
        if ($row && !empty($row['avatar'])
            && strpos($row['avatar'], '/assets/avatares/') === 0) {
            $filepath = $_SERVER['DOCUMENT_ROOT'] . $row['avatar'];
            if (file_exists($filepath) && is_file($filepath)) {
                @unlink($filepath);
            }
        }

        $response = ['success' => true, 'message' => 'Usuario eliminado correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al eliminar: ' . $stmt->error];
    }

    $stmt->close();

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);