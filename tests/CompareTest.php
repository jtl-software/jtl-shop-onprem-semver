<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Tests;

use JTLShop\SemVer\Compare;
use JTLShop\SemVer\Parser;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    /**
     * Testing equality of versions
     */
    public function testEquals(): void
    {
        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1'),
                Parser::parse('0.0.1')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.2'),
                Parser::parse('0.0.1')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1-alpha'),
                Parser::parse('0.0.1-alpha')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1-alpha'),
                Parser::parse('0.0.1-rc')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1-alpha.1'),
                Parser::parse('0.0.1-alpha.1')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1-alpha.1'),
                Parser::parse('0.0.1-alpha.2')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1-beta.12'),
                Parser::parse('0.0.1-beta.12')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1-beta.1'),
                Parser::parse('0.0.1-beta.2')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1-rc.123'),
                Parser::parse('0.0.1-rc.123')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1-rc.1'),
                Parser::parse('0.0.1-rc.2')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1+build.1'),
                Parser::parse('0.0.1+build.1')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1+build.1'),
                Parser::parse('0.0.1+build.2')
            )
        );

        $this->assertTrue(
            Compare::equals(
                Parser::parse('0.0.1-alpha.1+build.1'),
                Parser::parse('0.0.1-alpha.1+build.1')
            )
        );
        $this->assertFalse(
            Compare::equals(
                Parser::parse('0.0.1-alpha.1+build.1'),
                Parser::parse('0.0.1-alpha.2+build.1')
            )
        );
    }

    /**
     * Testing greater than compare of versions
     */
    public function testCompareVersionable(): void
    {
        $this->assertVersionBiggerThan('2.0.2', '0.0.4');
        $this->assertVersionBiggerThan('1.2.3', '1.2.2');
        $this->assertVersionBiggerThan('0.0.1', '0.0.0');
        // Check that versions that are equal are not bigger/smaller
        $this->assertFalse(
            Compare::greaterThan(
                Parser::parse('4.0.0'),
                Parser::parse('4.0.0')
            )
        );
        $this->assertFalse(
            Compare::smallerThan(
                Parser::parse('4.0.0'),
                Parser::parse('4.0.0')
            )
        );
    }

    /**
     * Test pre release comparison
     */
    public function testPreReleaseCompare(): void
    {
        // No prerelease versus pre-release
        $this->assertVersionBiggerThan('1.2.3', '1.2.3-alpha');
        $this->assertVersionBiggerThan('1.2.3', '1.2.3-rc.6');
        $this->assertVersionBiggerThan('1.2.3', '1.2.3-alpha.123');
        $this->assertVersionBiggerThan('1.2.3', '1.2.3-beta.2');
        $this->assertVersionBiggerThan('1.2.3', '1.0.0-beta.2');
        $this->assertVersionBiggerThan('2.0.0-beta.1', '1.0.0');
        // Greek
        $this->assertVersionBiggerThan('1.2.3-beta', '1.2.3-alpha.1');
        $this->assertVersionBiggerThan('1.2.3-beta.1', '1.2.3-alpha.1');
        $this->assertVersionBiggerThan('1.2.3-beta.1', '1.2.3-beta');
        $this->assertVersionBiggerThan('1.2.3-beta.2', '1.2.3-beta.1');
        $this->assertVersionBiggerThan('1.2.3-beta.2', '1.2.3-beta.1');
        $this->assertVersionBiggerThan('1.2.3-alpha.2', '1.2.3-alpha.1');
        $this->assertVersionBiggerThan('1.2.3-rc.2', '1.2.3-rc.1');
        $this->assertVersionBiggerThan('1.2.3-rc.4', '1.2.3-beta.6');
        $this->assertVersionBiggerThan('1.2.3-rc.6', '1.2.3-alpha.1');
        // Versionable
        $this->assertVersionBiggerThan('1.2.3-0.0.2', '1.2.3-0.0.1');
        $this->assertVersionBiggerThan('1.2.3-0.0.1', '1.2.2-0.0.2');
    }

    /**
     * Test comparison of build numbers
     */
    public function testBuildCompare(): void
    {
        $this->assertVersionBiggerThan('1.2.3+build', '1.2.3');
        $this->assertVersionBiggerThan('1.2.3+build.2', '1.2.3+build.1');
        $this->assertVersionBiggerThan('1.2.3+build.2.foo', '1.2.3+build.1.bar');
    }

    /**
     * Test comparison of pre-releases compared with builds
     */
    public function testBuildAndPreReleaseCompare(): void
    {
        $this->assertVersionBiggerThan('1.2.3+build', '1.2.3-rc.1');
        $this->assertVersionBiggerThan('1.2.3-rc.1+build', '1.2.3-rc.1');
        $this->assertVersionBiggerThan('1.2.3-rc.1+build.2', '1.2.3-rc.1+build.1');
        $this->assertVersionBiggerThan('1.2.3+build.2', '1.2.3-rc.1+build.2');
        $this->assertVersionBiggerThan('1.2.3-rc.1+build.3.foo', '1.2.3-rc.1+build.2');
    }

    /**
     * Test finding of greatest SemVer
     */
    public function testGreatest(): void
    {
        $v1 = Parser::parse('0.1.2');
        $v2 = Parser::parse('0.1.2-rc.1');
        $v3 = Parser::parse('0.1.2-rc');

        $this->assertEquals(Compare::greatest($v1, $v2, $v3), $v1);

        $v3 = Parser::parse('0.1.2-alpha+build.12345');
        $v4 = Parser::parse('0.1.3-beta');
        $v5 = Parser::parse('0.1.2');
        $v6 = Parser::parse('0.1.0');

        $this->assertEquals(Compare::greatest($v1, $v2, $v3, $v4, $v5, $v6), $v4);
    }

    /**
     * Assert that of two version strings the first is bigger than the other
     */
    private function assertVersionBiggerThan(string $v1String, string $v2String): void
    {
        $v1 = Parser::parse($v1String);
        $v2 = Parser::parse($v2String);
        // Versions should not be equal
        $this->assertFalse(
            Compare::equals($v1, $v2),
            \sprintf('Version "%s" should *not* be equal to "%s"', $v1, $v2)
        );
        // Greater than and not greater than
        $this->assertTrue(
            Compare::greaterThan($v1, $v2),
            \sprintf('Version "%s" should be greater than "%s"', $v1, $v2)
        );
        $this->assertFalse(
            Compare::greaterThan($v2, $v1),
            \sprintf('Version "%s" should be greater than "%s"', $v2, $v1)
        );
        // Smaller than and not smaller than
        $this->assertTrue(
            Compare::smallerThan($v2, $v1),
            \sprintf('Version "%s" should be smaller than "%s"', $v2, $v1)
        );
        $this->assertFalse(
            Compare::smallerThan($v1, $v2),
            \sprintf('Version "%s" should *not* be smaller than "%s"', $v1, $v2)
        );
    }
}
