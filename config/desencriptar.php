<?php
// $clave_secreta > openssl_decrypt_pass_cs.php
function desencriptar_datos($datos, $clave_secreta) {
    list($datos_base64, $iv_base64) = explode('::', $datos);
    $datos_decodificados = base64_decode(str_replace(['-', '_'], ['+', '/'], $datos_base64));
    $iv_decodificado = base64_decode(str_replace(['-', '_'], ['+', '/'], $iv_base64));
    return openssl_decrypt($datos_decodificados, 'aes-256-cbc', $clave_secreta, 0, $iv_decodificado);
}
// $data = desencriptar_datos(data, $clave_secreta);
