<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';

// requireAuth() corta con 401 si no está logueado, y ya manda Content-Type
$idUsuario = requireAuth();

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

try {
    $usuario_id = (int) $_SESSION['id'];
    $nombre     = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email      = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $avatar     = isset($_POST['avatar']) ? trim($_POST['avatar']) : '';   // URL opcional

    /* ---------- VALIDACIONES ---------- */
    if ($nombre === '') {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit;
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Ingrese un email válido']);
        exit;
    }

    /* ---------- AVATAR ACTUAL ---------- */
    $stmtPrev = mysqli_prepare($conex, "SELECT avatar FROM usuarios WHERE id = ?");
    mysqli_stmt_bind_param($stmtPrev, "i", $usuario_id);
    mysqli_stmt_execute($stmtPrev);
    $resPrev = mysqli_stmt_get_result($stmtPrev);
    $rowPrev = mysqli_fetch_assoc($resPrev);
    mysqli_stmt_close($stmtPrev);

    $avatarPrevio = $rowPrev['avatar'] ?? '';

    // Si no enviaron URL nueva, conservar la anterior
    if ($avatar === '') {
        $avatar = $avatarPrevio;
    }

    /* ---------- SUBIDA DE AVATAR ---------- */
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        $file    = $_FILES['avatar_file'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 2MB)']);
            exit;
        }

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/avatares';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $ext;
        $filepath = $upload_dir . '/' . $filename;
        $avatar   = '/assets/avatares/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el avatar']);
            exit;
        }

        // Borrar avatar anterior si era local
        if (!empty($avatarPrevio) && strpos($avatarPrevio, '/assets/avatares/') === 0) {
            $old_file = $_SERVER['DOCUMENT_ROOT'] . $avatarPrevio;
            if (file_exists($old_file) && is_file($old_file)) {
                unlink($old_file);
            }
        }
    }

    /* ---------- EMAIL ÚNICO ---------- */
    $check = mysqli_prepare($conex, "SELECT id FROM usuarios WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $email, $usuario_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Este email ya está registrado por otro usuario']);
        mysqli_stmt_close($check);
        exit;
    }
    mysqli_stmt_close($check);

    /* ---------- UPDATE ---------- */
    $query = "UPDATE usuarios SET nombre = ?, email = ?, avatar = ? WHERE id = ?";
    $stmt  = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $avatar, $usuario_id);

    if (mysqli_stmt_execute($stmt)) {
        // 🔄 Refrescar la sesión para que el sidebar/header muestren los nuevos datos
        $_SESSION['nombre'] = $nombre;
        $_SESSION['email']  = $email;
        $_SESSION['avatar'] = $avatar;

        $response = [
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'avatar'  => $avatar,
        ];
    } else {
        // Si falla el UPDATE y habíamos subido archivo nuevo, borrarlo
        if (isset($filepath) && !empty($filepath) && file_exists($filepath)) {
            unlink($filepath);
        }
        $response = ['success' => false, 'message' => 'Error al actualizar: ' . mysqli_stmt_error($stmt)];
    }

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);