<?php declare(strict_types=1);

spl_autoload_register(static function ($className) {
    // Only load "Abc" classes
    if (!str_starts_with($className, 'JTLShop')) {
        return false;
    }
    // Simple transliteration
    require_once __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';
});

// Require composer autoloader
include __DIR__ . '/../vendor/autoload.php';
