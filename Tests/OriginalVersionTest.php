<?php
/**
 * OriginalVersionTest.php
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 */

use MirkoSchmidt\SemVer\Parser;

use \PHPUnit_Framework_TestCase as TestCase;

/**
 * OriginalVersionTest
 *
 * Testing original version saving/retrieval
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 */
class OriginalVersionTest extends TestCase
{
    /**
     * Test sort of strings
     *
     * @return void
     **/
    public function testStoreOriginalVersion()
    {
        $version = Parser::parse('0.0.0');

        $version->setMinor(1);

        $this->assertEquals('0.0.0', $version->getOriginalVersion());
        $this->assertEquals('0.1.0', (string) $version);
    }
}
