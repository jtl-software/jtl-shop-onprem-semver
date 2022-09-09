<?php declare(strict_types=1);

use JTLShop\SemVer\Parser;
use PHPUnit\Framework\TestCase;

/**
 * OriginalVersionTest
 *
 * Testing original version saving/retrieval
 *
 * @category        Naneau
 * @package         SemVer
 */
class OriginalVersionTest extends TestCase
{
    /**
     * Test sort of strings
     *
     * @return void
     **/
    public function testStoreOriginalVersion(): void
    {
        $version  = Parser::parse('0.0.0');
        $version2 = \JTLShop\SemVer\Version::parse(406);

        $version->setMinor(1);

        $this->assertEquals('0.0.0', $version->getOriginalVersion());
        $this->assertEquals('0.1.0', (string) $version);
        $this->assertEquals('4.06.0', (string) $version2);
    }
}
