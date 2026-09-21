<?php
require_once __DIR__ . '/../../config/conex.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/openssl_decrypt_pass_cs.php';
require_once __DIR__ . '/../../config/encriptar.php';

$idUsuario = requireAuth();   // usuario logueado (admin)

header('Content-Type: application/json');

if (!$conex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

try {
    $usuario_id    = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
    $password_new  = isset($_POST['password_new']) ? trim($_POST['password_new']) : '';
    $password_conf = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';

    if ($usuario_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario inválido']);
        exit;
    }

    if ($password_new === '') {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña es requerida']);
        exit;
    }

    if ($password_new !== $password_conf) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
        exit;
    }

    // Validar que el usuario exista
    $check = $conex->prepare("SELECT id FROM usuarios WHERE id = ?");
    $check->bind_param('i', $usuario_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        $check->close();
        exit;
    }
    $check->close();

    // Encriptar y actualizar
    $passwordEncriptada = encriptar_datos($password_new, $clave_secreta, $iv);

    $query = "UPDATE usuarios SET password = ? WHERE id = ?";
    $stmt  = $conex->prepare($query);
    $stmt->bind_param('si', $passwordEncriptada, $usuario_id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error];
    }

    $stmt->close();

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);