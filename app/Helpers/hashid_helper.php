<?php

/**
 * Custom ID Obfuscator Helper for CodeIgniter 4
 * Mengubah ID int ke string hash acak menggunakan Base36 dan salt.
 */

if (!function_exists('encode_id')) {
    function encode_id($id)
    {
        if (empty($id) || !is_numeric($id)) {
            return $id;
        }
        
        $salt = 928374; // Custom salt value
        $number = (int) $id + $salt;
        
        // Convert to base 36 (0-9, a-z)
        $encoded = base_convert($number, 10, 36);
        
        // Add random prefix/suffix to make it look less sequential
        $prefix = substr(md5($id), 0, 2); 
        $suffix = substr(md5($id . $salt), -2);
        
        return $prefix . $encoded . $suffix;
    }
}

if (!function_exists('decode_id')) {
    function decode_id($hash)
    {
        if (empty($hash) || strlen($hash) < 5) {
            return null;
        }

        // Remove the 2-char prefix and 2-char suffix
        $encoded = substr($hash, 2, -2);
        
        if (empty($encoded)) {
            return null;
        }

        $salt = 928374;
        $number = (int) base_convert($encoded, 36, 10);
        $id = $number - $salt;
        
        // Basic validation
        if ($id <= 0) {
            return null;
        }
        
        return $id;
    }
}
