<?php

// Enable Error Reporting based on Environment
// Note: Env::load is called later, so we might need to move this after Env::load
// OR, we can just set it securely to off by default and let local dev turn it on.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Log errors, but don't show them to user

// Basic Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/'; // Current directory is src/

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load Environment and Config
use App\Config\Config;
Config::load();
