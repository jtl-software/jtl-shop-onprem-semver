<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Tests;

\spl_autoload_register(static function ($className) {
    // Only load our classes
    if (!\str_starts_with($className, 'JTLShop')) {
        return;
    }
    require_once __DIR__ . '/../src/' . \str_replace('\\', '/', $className) . '.php';
});

include __DIR__ . '/../vendor/autoload.php';
