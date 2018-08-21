<?php
/**
 * CleanCopyVersionTest.php
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 */

use MirkoSchmidt\SemVer\Parser;

use \PHPUnit_Framework_TestCase as TestCase;

/**
 * CleanCopyVersionTest
 *
 * Testing Version::cleanCopy()
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 */
class CleanCopyVersionTest extends TestCase
{
    public function testIncludesStandardVersion()
    {
        $version = Parser::parse('1.2.3');
        $this->assertEquals('1.2.3', (string) $version->cleanCopy());
    }

    public function testIncludePreRelease()
    {
        $version = Parser::parse('1.0.0-beta.1');
        $this->assertEquals('1.0.0-beta.1', (string) $version->cleanCopy());
    }

    public function testDiscardsBuild()
    {
        $version = Parser::parse('1.0.0+build');
        $this->assertEquals('1.0.0', (string) $version->cleanCopy());
    }

    public function testDiscardsOriginalString()
    {
        $version = Parser::parse('1.0.0');
        $this->assertEquals('', $version->cleanCopy()->getOriginalVersion());
    }
}
