<?php
// Configuración CORS para permitir peticiones desde tu frontend React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Responder a las peticiones OPTIONS (preflight de CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración del servidor de correo
define('SMTP_HOST', 'mail.argenmedicalresidencia.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'contacto@argenmedicalresidencia.com');
define('SMTP_PASS', '1fvy%2f8@e@%');
define('SMTP_FROM', 'contacto@argenmedicalresidencia.com');
define('SMTP_FROM_NAME', 'Contacto web - Argen Medical');
define('SMTP_TO', 'argenmedical.residencia@gmail.com');
define('SMTP_TO_NAME', 'Argen Medical');

// Verificar que sea método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener datos del body (JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Si no es JSON, intentar con POST normal
    $input = $_POST;
}

// Validar campos (mapeo de name/email/subject/message)
$nombre = trim($input['name'] ?? '');
$correo = trim($input['email'] ?? '');
$asunto = trim($input['subject'] ?? '');
$mensaje_usuario = trim($input['message'] ?? '');

$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre completo es requerido';
}
if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido';
}
if (empty($asunto)) {
    $errores[] = 'El asunto es requerido';
}
if (empty($mensaje_usuario)) {
    $errores[] = 'El mensaje es requerido';
}

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos', 'errors' => $errores]);
    exit();
}

// CORREO QUE RECIBE EL SOPORTE (TÚ)
$mensaje_html_para_ti = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje - Argen Medical</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; padding: 30px; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 35px -10px rgba(0,0,0,0.15); }
        .header { background: linear-gradient(135deg, #005B9F 0%, #009CFF 100%); padding: 30px; text-align: center; color: white; }
        .logo { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
        .content { padding: 35px 30px; }
        .info-card { background: #F8FAFE; border-radius: 16px; padding: 20px; margin-bottom: 25px; border: 1px solid #E3EDF7; }
        .info-row { margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #E9EFF4; }
        .info-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #009CFF; font-weight: 700; margin-bottom: 6px; }
        .info-value { font-size: 16px; color: #1a2a3a; font-weight: 500; word-break: break-word; }
        .message-box { background: #F0F7FF; border-left: 4px solid #009CFF; padding: 18px 20px; border-radius: 12px; margin-top: 10px; }
        .footer { background: #F5F7FA; padding: 20px; text-align: center; font-size: 12px; color: #6c7a8a; }
        @media (max-width: 550px) { .content { padding: 25px 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Argen Medical</div>
            <h2>✉️ Nuevo mensaje de contacto web</h2>
        </div>
        <div class="content">
            <div class="info-card">
                <div class="info-row">
                    <div class="info-label">👤 Nombre</div>
                    <div class="info-value">' . htmlspecialchars($nombre) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">📧 Correo</div>
                    <div class="info-value">' . htmlspecialchars($correo) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">📌 Asunto</div>
                    <div class="info-value">' . htmlspecialchars($asunto) . '</div>
                </div>
            </div>
            <div class="info-label">💬 Mensaje</div>
            <div class="message-box">
                <p>' . nl2br(htmlspecialchars($mensaje_usuario)) . '</p>
            </div>
        </div>
        <div class="footer">
            <p><strong>Argen Medical</strong><br>Responder a: ' . htmlspecialchars($correo) . '</p>
        </div>
    </div>
</body>
</html>
';

$mensaje_texto_para_ti = "=== NUEVO MENSAJE ARGEN MEDICAL ===\n\n";
$mensaje_texto_para_ti .= "Nombre: $nombre\n";
$mensaje_texto_para_ti .= "Email: $correo\n";
$mensaje_texto_para_ti .= "Asunto: $asunto\n";
$mensaje_texto_para_ti .= "Mensaje:\n$mensaje_usuario\n";

// Confirmación para el usuario
$confirmacion_html = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background: #f4f7fc; padding: 30px;">
    <div style="max-width: 500px; margin: 0 auto; background: white; border-radius: 16px; padding: 30px; text-align: center;">
        <div style="font-size: 48px;">✅</div>
        <h2 style="color: #005B9F;">¡Mensaje enviado!</h2>
        <p>Hola <strong>' . htmlspecialchars($nombre) . '</strong>,</p>
        <p>Hemos recibido tu mensaje <strong>"' . htmlspecialchars($asunto) . '"</strong> correctamente.</p>
        <div style="background: #E8F3FF; padding: 15px; border-radius: 12px; margin: 20px 0;">
            <p style="margin: 0; color: #005B9F;">📞 Te responderemos a la brevedad.</p>
        </div>
        <p style="font-size: 12px; color: #6c7a8a;">Argen Medical</p>
    </div>
</body>
</html>
';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$mail = new PHPMailer(true);
$response = ['success' => false, 'message' => ''];

try {
    // Configuración SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = SMTP_PORT;
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    
    // --- CORREO PARA TI (soporte) ---
    $mail->clearAddresses();
    $mail->addAddress(SMTP_TO, SMTP_TO_NAME);
    $mail->addReplyTo($correo, $nombre);
    $mail->isHTML(true);
    $mail->Subject = "Contacto web Argen Medical: $asunto - $nombre";
    $mail->Body    = $mensaje_html_para_ti;
    $mail->AltBody = $mensaje_texto_para_ti;
    $mail->send();
    
    // --- CORREO DE CONFIRMACIÓN PARA EL USUARIO ---
    $mail->clearAddresses();
    $mail->addAddress($correo, $nombre);
    $mail->Subject = "Hemos recibido tu mensaje - Argen Medical";
    $mail->Body    = $confirmacion_html;
    $mail->AltBody = "Hola $nombre, hemos recibido tu mensaje. Te responderemos pronto.\n\nArgen Medical";
    $mail->send();
    
    $response['success'] = true;
    $response['message'] = 'Mensaje enviado correctamente';
    http_response_code(200);
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error al enviar: ' . $mail->ErrorInfo;
    http_response_code(500);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>