<?php

spl_autoload_register(static function ($className) {

    // Only load "Abc" classes
    if (strpos($className, 'JTLShop') !== 0) {
        return false;
    }

    // Simple transliteration
    require_once __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';
});

// Require composer autoloader
include __DIR__ . '/../vendor/autoload.php';
