<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Tests;

use InvalidArgumentException;
use JTLShop\SemVer\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Testing retrieving the next Version from a Version
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
        $version->next(new stdClass());
    }

    public function testNextThrowsExceptionOnPreReleaseBaseVersionWhenVersionNotPreRelease(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $version = Parser::parse('1.0.0');
        $version->next('1.0.0-rc');
    }

    #[DataProvider('versionStringProvider')]
    public function test(string $expected, string $base_str, string $current_str): void
    {
        $base    = Parser::parse($base_str);
        $current = Parser::parse($current_str);

        $this->assertEquals($expected, (string)$current->next($base));
    }

    /**
     * @return array<string[]>
     */
    public static function versionStringProvider(): array
    {
        return [
            // Move to next significant release
            ['2.0.0', '2.0.0', '1.0.0'],
            ['1.2.0', '1.2.0', '1.1.0'],
            ['1.0.0', '1.0.0', '1.0.0-beta'],
            ['1.0.0-rc', '1.0.0-rc', '1.0.0-beta'],
            ['1.0.0-beta', '1.0.0-beta', '1.0.0-alpha'],
            ['2.0.0-alpha', '2.0.0-alpha', '1.9.0'],
            ['2.1.0-alpha', '2.1.0-alpha', '2.0.0'],
            ['2.1.1-alpha', '2.1.1-alpha', '2.1.0'],
            // inc patch
            ['1.0.1', '1.0.0', '1.0.0'],
            ['1.0.2', '1.0.0', '1.0.1'],
            ['1.0.2', '1.0.2', '1.0.1'],
            // inc prerelase number
            ['1.0.0-beta.1', '1.0.0-beta', '1.0.0-beta'],
            ['1.0.0-beta.2', '1.0.0-beta', '1.0.0-beta.1'],
            ['1.0.0-beta.6000', '1.0.0-beta', '1.0.0-beta.5999'],
            // Ignore build string
            ['1.0.1', '1.0.0', '1.0.0+123421'],
            ['1.0.0-beta.1', '1.0.0-beta', '1.0.0-beta.0+123421'],
        ];
    }
}
