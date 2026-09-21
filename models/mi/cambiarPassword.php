<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/openssl_decrypt_pass_cs.php';
require_once __DIR__ . '/../../config/encriptar.php';
require_once __DIR__ . '/../../config/desencriptar.php';

$idUsuario = requireAuth();

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

try {
    $usuario_id       = (int) $_SESSION['id'];
    $password_current = isset($_POST['password_current']) ? $_POST['password_current'] : '';
    $password_new     = isset($_POST['password_new']) ? $_POST['password_new'] : '';

    if ($password_current === '' || $password_new === '') {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no pueden estar vacías']);
        exit;
    }

    if (strlen($password_new) < 6) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']);
        exit;
    }

    // Obtener contraseña actual
    $query = "SELECT password FROM usuarios WHERE id = ?";
    $stmt  = mysqli_prepare($conex, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    $passwordDesencriptada = desencriptar_datos($user['password'], $clave_secreta);
    if ($passwordDesencriptada !== $password_current) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit;
    }

    $passwordEncriptada = encriptar_datos($password_new, $clave_secreta, $iv);

    $updateQuery = "UPDATE usuarios SET password = ? WHERE id = ?";
    $updateStmt  = mysqli_prepare($conex, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "si", $passwordEncriptada, $usuario_id);

    if (mysqli_stmt_execute($updateStmt)) {
        $response = ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar: ' . mysqli_stmt_error($updateStmt)];
    }
    mysqli_stmt_close($updateStmt);

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);