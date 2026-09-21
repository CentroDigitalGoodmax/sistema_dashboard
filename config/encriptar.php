<?php
// $clave_secreta > openssl_decrypt_pass_cs.php
function encriptar_datos($datos, $clave_secreta, $iv) {
    $encriptado = openssl_encrypt($datos, 'aes-256-cbc', $clave_secreta, 0, $iv);
    $encriptado_base64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($encriptado));
    $iv_base64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($iv));
    return $encriptado_base64 . '::' . $iv_base64;
}
$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));

// $data = encriptar_datos($data, $clave_secreta, $iv);
