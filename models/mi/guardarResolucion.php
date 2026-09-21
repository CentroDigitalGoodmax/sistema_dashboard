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
    $persona = isset($_POST['persona']) ? trim($_POST['persona']) : '';
    $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
    $imagen_actual = isset($_POST['imagen_actual']) ? trim($_POST['imagen_actual']) : '';

    // Validaciones
    if (empty($persona)) {
        echo json_encode(['success' => false, 'message' => 'El nombre de la persona es requerido']);
        exit;
    }

    // Procesar imagen si se subió
    $imagen_url = $imagen_actual;
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $file = $_FILES['imagen'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }

        // Crear directorio de subidas si no existe
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/resoluciones';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generar nombre único para el archivo
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('res_') . '.' . $ext;
        $filepath = $upload_dir . '/' . $filename;
        $imagen_url = '/assets/resoluciones/' . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen']);
            exit;
        }

        // Si es actualización y tiene imagen anterior, eliminar la anterior
        if ($id > 0 && !empty($imagen_actual)) {
            $old_file = $_SERVER['DOCUMENT_ROOT'] . $imagen_actual;
            if (file_exists($old_file) && is_file($old_file)) {
                unlink($old_file);
            }
        }
    } else {
        // Si es nuevo y no hay imagen, error
        if ($id == 0) {
            echo json_encode(['success' => false, 'message' => 'Debes seleccionar una imagen']);
            exit;
        }
    }

    if ($id > 0) {
        // ACTUALIZAR
        $query = "UPDATE resoluciones SET 
                    persona = ?, 
                    img = ?, 
                    estado = ?
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "ssii", 
            $persona, 
            $imagen_url, 
            $estado, 
            $id
        );
    } else {
        // INSERTAR
        $query = "INSERT INTO resoluciones (persona, img, estado) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "ssi", 
            $persona, 
            $imagen_url, 
            $estado
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        $response = ['success' => true, 'message' => 'Resolución guardada correctamente'];
        if ($id == 0) {
            $response['id'] = mysqli_insert_id($conex);
        }
        $response['imagen_url'] = $imagen_url;
    } else {
        $response = ['success' => false, 'message' => 'Error al guardar: ' . mysqli_stmt_error($stmt)];
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>