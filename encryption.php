<?php
/**
 * Encrypt a string using AES-128-ECB and return a base64-encoded, URL-safe string.
 *
 * @param string $string - The data to encrypt (e.g., product ID)
 * @param string $key - Secret key used for encryption
 * @return string - Encrypted and base64-encoded value
 */
function encrypt($string, $key = 'secretkey') {
    // Encrypt the string using AES
    $encrypted = openssl_encrypt($string, 'AES-128-ECB', $key);
    // Encode with base64 and make it URL-safe
    return urlencode(base64_encode($encrypted));
}

/**
 * Decrypt a URL-safe base64 string back to its original form.
 *
 * @param string $string - The encrypted data
 * @param string $key - Same key used for encryption
 * @return string - Original decrypted value
 */
function decrypt($string, $key = 'secretkey') {
    // Decode and decrypt
    return openssl_decrypt(base64_decode(urldecode($string)), 'AES-128-ECB', $key);
}
?>
