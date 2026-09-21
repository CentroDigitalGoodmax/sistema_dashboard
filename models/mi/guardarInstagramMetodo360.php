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
    $id = isset($_POST['id']) && $_POST['id'] != '' ? intval($_POST['id']) : 0;
    $reel = isset($_POST['reel']) ? trim($_POST['reel']) : '';
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
    
    // Validaciones
    if (empty($reel)) {
        echo json_encode(['success' => false, 'message' => 'El ID del Reel es requerido']);
        exit;
    }
    
    if (empty($titulo)) {
        echo json_encode(['success' => false, 'message' => 'El título es requerido']);
        exit;
    }
    
    // Validar que el reel sea un ID válido
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $reel)) {
        echo json_encode(['success' => false, 'message' => 'ID de metodo360 inválido']);
        exit;
    }
    
    if ($id > 0) {
        // ACTUALIZAR
        $query = "UPDATE metodo360 SET 
                    reel = ?, 
                    titulo = ?, 
                    estado = ?
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "ssii", 
            $reel, 
            $titulo, 
            $estado, 
            $id
        );
    } else {
        // INSERTAR
        $query = "INSERT INTO metodo360 (reel, titulo, estado) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "ssi", 
            $reel, 
            $titulo, 
            $estado
        );
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Testimonio guardado correctamente'];
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