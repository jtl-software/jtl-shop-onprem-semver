<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Tests;

use JTLShop\SemVer\Parser;
use JTLShop\SemVer\Version;
use PHPUnit\Framework\TestCase;

class OriginalVersionTest extends TestCase
{
    /**
     * Test sort of strings
     */
    public function testStoreOriginalVersion(): void
    {
        $version  = Parser::parse('0.0.0');
        $version2 = Version::parse(406);

        $version->setMinor(1);

        $this->assertEquals('0.0.0', $version->getOriginalVersion());
        $this->assertEquals('0.1.0', (string)$version);
        $this->assertEquals('4.6.0', (string)$version2);
    }
}
