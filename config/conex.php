<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'sistema_dashboard');
define('DB_PASS', '***************');
define('DB_NAME', 'sistema_dashboard');
define('DB_CHARSET', 'utf8mb4');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conex = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conex->set_charset(DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    error_log('Error de conexión MySQL: ' . $e->getMessage());
    http_response_code(500);
    exit('Error interno de servidor. No se pudo conectar a la base de datos.');
}
?>
