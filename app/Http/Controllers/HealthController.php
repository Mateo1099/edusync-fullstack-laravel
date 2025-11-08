<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function openssl()
    {
        return response()->json([
            'openssl_loaded' => extension_loaded('openssl'),
            'openssl_functions_present' => function_exists('openssl_cipher_iv_length'),
            'openssl_version' => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : null,
            'php_version' => PHP_VERSION,
            'php_ini_loaded_file' => function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : null,
            'php_ini_scanned_files' => function_exists('php_ini_scanned_files') ? php_ini_scanned_files() : null,
            'extension_dir' => ini_get('extension_dir') ?: null,
        ]);
    }
}
