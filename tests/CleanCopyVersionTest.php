<?php declare(strict_types=1);

use JTLShop\SemVer\Parser;
use PHPUnit\Framework\TestCase;

/**
 * CleanCopyVersionTest
 *
 * Testing Version::cleanCopy()
 *
 * @category        Naneau
 * @package         SemVer
 */
class CleanCopyVersionTest extends TestCase
{
    public function testIncludesStandardVersion(): void
    {
        $version = Parser::parse('1.2.3');
        $this->assertEquals('1.2.3', (string)$version->cleanCopy());
    }

    public function testIncludePreRelease(): void
    {
        $version = Parser::parse('1.0.0-beta.1');
        $this->assertEquals('1.0.0-beta.1', (string)$version->cleanCopy());
    }

    public function testDiscardsBuild(): void
    {
        $version = Parser::parse('1.0.0+build');
        $this->assertEquals('1.0.0', (string)$version->cleanCopy());
    }

    public function testDiscardsOriginalString(): void
    {
        $version = Parser::parse('1.0.0');
        $this->assertEquals('', $version->cleanCopy()->getOriginalVersion());
    }
}
