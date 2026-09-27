<?php


const ENCRYPT_KEY = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';



/**
* Encrypt a message
* PHP ext required : sodium, mb_strlen
* @param string $message - message to encrypt
* @param string $key - encryption key
* @return string
*/
function safeEncrypt($message, $key = ENCRYPT_KEY)
{
    $nonce = random_bytes(
        SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
    );

    $cipher = base64_encode(
        $nonce.
        sodium_crypto_secretbox(
            $message,
            $nonce,
            $key
        )
    );
    sodium_memzero($message);
    sodium_memzero($key);
    return $cipher;
}

    /**
    * Decrypt a message
    * PHP ext required : sodium, mb_strlen
    * @param string $encrypted - message encrypted with safeEncrypt()
    * @param string $key - encryption key
    * @return string
    */
    function safeDecrypt($encrypted, $key = ENCRYPT_KEY)
    {   
        $decoded = base64_decode($encrypted);
        if ($decoded === false) {
            throw new Exception('Scream bloody murder, the encoding failed');
        }
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
            throw new Exception('Scream bloody murder, the message was truncated');
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if ($plain === false) {
            throw new Exception('the message was tampered with in transit');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }

?>