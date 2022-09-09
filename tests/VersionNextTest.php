<?php declare(strict_types=1);

use JTLShop\SemVer\Parser;
use PHPUnit\Framework\TestCase;

/**
 * VersionNextTest
 *
 * Testing retrieving the next Version from a Version
 *
 * @category        Naneau
 * @package         SemVer
 */
class VersionNextTest extends TestCase
{
    public function testNextAcceptsNoArg(): void
    {
        $version = Parser::parse('1.0.0');
        $this->assertEquals('1.0.1', $version->next());
    }

    public function testNextAcceptsEmpty(): void
    {
        $version = Parser::parse('1.0.0');
        $this->assertEquals('1.0.1', $version->next(false));
    }

    public function testNextAcceptsString(): void
    {
        $version = Parser::parse('1.0.0');
        $this->assertEquals('1.0.2', $version->next('1.0.2'));
    }

    public function testNextThrowsExceptionOnUnhandledInputType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $version = Parser::parse('1.0.0');
        $version->next(new stdclass());
    }

    public function testNextThrowsExceptionOnPreReleaseBaseVersionWhenVersionNotPreRelease(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $version = Parser::parse('1.0.0');
        $version->next('1.0.0-rc');
    }

    /**
     * @dataProvider versionStringProvider
     */
    public function test($expected, $base_str, $current_str): void
    {
        $base    = Parser::parse($base_str);
        $current = Parser::parse($current_str);

        $this->assertEquals($expected, (string)$current->next($base));
    }

    public function versionStringProvider(): array
    {
        return [
            // Move to next significant release
            array('2.0.0', '2.0.0', '1.0.0'),
            array('1.2.0', '1.2.0', '1.1.0'),
            array('1.0.0', '1.0.0', '1.0.0-beta'),
            array('1.0.0-rc', '1.0.0-rc', '1.0.0-beta'),
            array('1.0.0-beta', '1.0.0-beta', '1.0.0-alpha'),
            array('2.0.0-alpha', '2.0.0-alpha', '1.9.0'),
            array('2.1.0-alpha', '2.1.0-alpha', '2.0.0'),
            array('2.1.1-alpha', '2.1.1-alpha', '2.1.0'),

            // inc patch
            array('1.0.1', '1.0.0', '1.0.0'),
            array('1.0.2', '1.0.0', '1.0.1'),
            array('1.0.2', '1.0.2', '1.0.1'),

            // inc prerelase number
            array('1.0.0-beta.1', '1.0.0-beta', '1.0.0-beta'),
            array('1.0.0-beta.2', '1.0.0-beta', '1.0.0-beta.1'),
            array('1.0.0-beta.6000', '1.0.0-beta', '1.0.0-beta.5999'),

            // Ignore build string
            array('1.0.1', '1.0.0', '1.0.0+123421'),
            array('1.0.0-beta.1', '1.0.0-beta', '1.0.0-beta.0+123421'),
        ];
    }
}
