<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/openssl_decrypt_pass_cs.php';
require_once __DIR__ . '/../../config/encriptar.php';

// requireAuth() corta con 401 si no está logueado, y ya manda Content-Type
$idUsuario = requireAuth();

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

try {
    $id       = isset($_POST['id']) && $_POST['id'] != '' ? intval($_POST['id']) : 0;
    $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email    = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $avatar   = isset($_POST['avatar']) ? trim($_POST['avatar']) : '';   // URL opcional
    $activo   = isset($_POST['activo']) ? intval($_POST['activo']) : 1;

    // Validaciones
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Ingrese un email válido']);
        exit;
    }

    // Guardar avatar previo si es edición (para no perderlo cuando no se sube nada)
    $avatarPrevio = '';
    if ($id > 0) {
        $stmtPrev = mysqli_prepare($conex, "SELECT avatar FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmtPrev, "i", $id);
        mysqli_stmt_execute($stmtPrev);
        $resPrev = mysqli_stmt_get_result($stmtPrev);
        $rowPrev = mysqli_fetch_assoc($resPrev);
        mysqli_stmt_close($stmtPrev);

        if ($rowPrev) {
            $avatarPrevio = $rowPrev['avatar'] ?? '';
        }
    }

    // Si es edición y no mandaron nada, conservar el avatar previo
    if ($id > 0 && $avatar === '') {
        $avatar = $avatarPrevio;
    }

    // Procesar archivo de avatar si se subió
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        $file    = $_FILES['avatar_file'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido']);
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB máximo
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (máximo 2MB)']);
            exit;
        }

        // Crear directorio de subidas si no existe
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/avatares';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generar nombre único para el archivo
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $ext;
        $filepath = $upload_dir . '/' . $filename;
        $avatar   = '/assets/avatares/' . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar el avatar']);
            exit;
        }

        // Si es actualización y tenía avatar local anterior, eliminarlo
        if ($id > 0 && !empty($avatarPrevio) && strpos($avatarPrevio, '/assets/avatares/') === 0) {
            $old_file = $_SERVER['DOCUMENT_ROOT'] . $avatarPrevio;
            if (file_exists($old_file) && is_file($old_file)) {
                unlink($old_file);
            }
        }
    }

    // Crear o actualizar
    if ($id > 0) {
        // Verificar email único (excluyendo el propio usuario)
        $check = mysqli_prepare($conex, "SELECT id FROM usuarios WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $email, $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email ya está registrado']);
            mysqli_stmt_close($check);
            exit;
        }
        mysqli_stmt_close($check);

        if (!empty($password)) {
            // Actualizar incluyendo contraseña
            $passwordEncriptada = encriptar_datos($password, $clave_secreta, $iv);
            $query = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, avatar = ?, activo = ? WHERE id = ?";
            $stmt  = mysqli_prepare($conex, $query);
            mysqli_stmt_bind_param($stmt, "ssssii", $nombre, $email, $passwordEncriptada, $avatar, $activo, $id);
        } else {
            // Actualizar sin tocar contraseña
            $query = "UPDATE usuarios SET nombre = ?, email = ?, avatar = ?, activo = ? WHERE id = ?";
            $stmt  = mysqli_prepare($conex, $query);
            mysqli_stmt_bind_param($stmt, "sssii", $nombre, $email, $avatar, $activo, $id);
        }

    } else {
        // Crear nuevo usuario
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'La contraseña es requerida para nuevos usuarios']);
            exit;
        }

        // Verificar email único
        $check = mysqli_prepare($conex, "SELECT id FROM usuarios WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email ya está registrado']);
            mysqli_stmt_close($check);
            exit;
        }
        mysqli_stmt_close($check);

        $passwordEncriptada = encriptar_datos($password, $clave_secreta, $iv);

        $query = "INSERT INTO usuarios (nombre, email, password, avatar, activo, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt  = mysqli_prepare($conex, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $email, $passwordEncriptada, $avatar, $activo);
    }

    if (mysqli_stmt_execute($stmt)) {
        $response = [
            'success' => true,
            'message' => $id > 0 ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente',
        ];
        if ($id == 0) {
            $response['id'] = mysqli_insert_id($conex);
        }
        
        if ($id > 0 && (int) $id === (int) $idUsuario) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email']  = $email;
            $_SESSION['avatar'] = $avatar;
        }

        if (!empty($avatar)) {
            $response['avatar'] = $avatar;
        }
    } else {
        // Si falla el INSERT/UPDATE y habíamos subido un archivo nuevo, borrarlo
        if (isset($filepath) && !empty($filepath) && file_exists($filepath)) {
            unlink($filepath);
        }
        $response = ['success' => false, 'message' => 'Error al guardar: ' . mysqli_stmt_error($stmt)];
    }

    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);