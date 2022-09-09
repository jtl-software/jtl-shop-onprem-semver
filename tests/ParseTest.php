<?php declare(strict_types=1);

use JTLShop\SemVer\Parser;
use PHPUnit\Framework\TestCase;

/**
 * ParseTest
 *
 * Testing the parsers
 *
 * @category      Naneau
 * @package       SemVer
 * @subpackage    Tests
**/
class ParseTest extends TestCase
{
    /**
     * Test parsing of versionable X.Y.Z
     *
     * @return void
     **/
    public function testVersionable(): void
    {
        $v1 = Parser::parse('0.1.1-rc.1');
        $v2 = Parser::parse('1.111.2-alpha.1235+build.4');
        $v3 = Parser::parse('20.0.123+build.6');
        $v4 = \JTLShop\SemVer\Version::parse(406);

        // Major
        $this->assertEquals(
            0,
            $v1->getMajor(),
            'Major should parse'
        );
        $this->assertEquals(
            1,
            $v2->getMajor(),
            'Major should parse'
        );
        $this->assertEquals(
            20,
            $v3->getMajor(),
            'Major should parse'
        );
        $this->assertEquals(
            4,
            $v4->getMajor(),
            'v4 Major should parse'
        );

        // Minor
        $this->assertEquals(
            1,
            $v1->getMinor(),
            'Minor should parse'
        );
        $this->assertEquals(
            111,
            $v2->getMinor(),
            'Minor should parse'
        );
        $this->assertEquals(
            0,
            $v3->getMinor(),
            'Minor should parse'
        );
        $this->assertEquals(
            6,
            $v4->getMinor(),
            'v4 Minor should parse'
        );

        // Patch
        $this->assertEquals(
            1,
            $v1->getPatch(),
            'Patch should parse'
        );
        $this->assertEquals(
            2,
            $v2->getPatch(),
            'Patch should parse'
        );
        $this->assertEquals(
            123,
            $v3->getPatch(),
            'Patch should parse'
        );
        $this->assertEquals(
            0,
            $v4->getPatch(),
            'v4 Patch should parse'
        );
    }

    /**
     * Test pre release parser in greek form
     *
     * @return void
     **/
    public function testPreReleaseGreek(): void
    {
        // Greek string
        $this->assertEquals(
            'alpha',
            Parser::parse('0.0.1-alpha.1')->getPreRelease()->getGreek()
        );
        $this->assertEquals(
            'beta',
            Parser::parse('0.0.1-beta')->getPreRelease()->getGreek()
        );
        $this->assertEquals(
            'rc',
            Parser::parse('0.0.1-rc.1')->getPreRelease()->getGreek()
        );
        $this->assertEquals(
            'foo',
            Parser::parse('0.0.1-foo.1')->getPreRelease()->getGreek()
        );

        // Release number
        $this->assertEquals(
            0,
            Parser::parse('0.0.1-alpha')->getPreRelease()->getReleaseNumber()
        );
        $this->assertEquals(
            1,
            Parser::parse('0.0.1-alpha.1')->getPreRelease()->getReleaseNumber()
        );
        $this->assertEquals(
            2,
            Parser::parse('0.0.1-beta.2')->getPreRelease()->getReleaseNumber()
        );
        $this->assertEquals(
            123,
            Parser::parse('0.0.1-rc.123')->getPreRelease()->getReleaseNumber()
        );

        // Make sure versionable doesn't parse into positive ints
        $this->assertEquals(
            0,
            Parser::parse('0.0.1-alpha.1')->getPreRelease()->getMajor()
        );
        $this->assertEquals(
            0,
            Parser::parse('0.0.1-alpha.1')->getPreRelease()->getMinor()
        );
        $this->assertEquals(
            0,
            Parser::parse('0.0.1-alpha.1')->getPreRelease()->getPatch()
        );
    }

    /**
     * Test pre release parser in greek form
     *
     * @return void
     **/
    public function testPreReleaseVersionable(): void
    {
        $this->assertEquals(
            0,
            Parser::parse('0.0.1-0.1.2')->getPreRelease()->getMajor()
        );
        $this->assertEquals(
            1,
            Parser::parse('0.0.1-0.1.2')->getPreRelease()->getMinor()
        );
        $this->assertEquals(
            123,
            Parser::parse('0.0.1-0.1.123')->getPreRelease()->getPatch()
        );

        // Make sure there are no greek parts
        $this->assertEmpty(Parser::parse('0.0.1-0.1.123')->getPreRelease()->getGreek());
        $this->assertEquals('0.1.123', Parser::parse('0.0.1-0.1.123')->getPreRelease()->__toString());
    }

    /**
     * Test the detection of builds
     *
     * @return void
     **/
    public function testBuildDetection(): void
    {
        $this->assertTrue(Parser::parse('0.0.1+build.1')->hasBuild());
        $this->assertFalse(Parser::parse('0.0.1')->hasBuild());
        $this->assertFalse(Parser::parse('0.0.1-alpha.1')->hasBuild());
    }

    /**
     * Test the build parser
     *
     * @return void
     **/
    public function testBuildNumber(): void
    {
        $this->assertEquals(
            1,
            Parser::parse('0.0.1+build.1')->getBuild()->getNumber()
        );
        $this->assertEquals(
            0,
            Parser::parse('0.0.1+build')->getBuild()->getNumber()
        );
        $this->assertEquals(
            3,
            Parser::parse('0.0.1-alpha.12345+build.3')->getBuild()->getNumber()
        );
        $this->assertEquals(
            12345,
            Parser::parse('0.0.1+build.12345.aaaaaa')->getBuild()->getNumber()
        );

    }

    /**
     * Test the parsing of remaining parts in the build version
     *
     * @return void
     **/
    public function testBuildParts(): void
    {
        $this->assertTrue(
            in_array('aaaaaa', Parser::parse('0.0.1+build.12345.aaaaaa.bbbbbb.cccccc')->getBuild()->getParts(), true)
        );
        $this->assertTrue(
            in_array('bbbbbb', Parser::parse('0.0.1+build.aaaaaa.bbbbbb.cccccc')->getBuild()->getParts(), true)
        );
        $this->assertTrue(
            in_array('cccccc', Parser::parse('0.0.1+build.12345.aaaaaa.bbbbbb.cccccc')->getBuild()->getParts(), true)
        );
    }

    /**
     * Test invalid version
     *
     * @return void
     **/
    public function testInvalid1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Parser::parse('foo.1.1');
    }

    /**
     * Test invalid version
     *
     * @return void
     **/
    public function testInvalid2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Parser::parse('0.foo.1');
    }

    /**
     * Test invalid version
     *
     * @return void
     **/
    public function testInvalid3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Parser::parse('10.1.foo');
    }

    /**
     * Test invalid version
     *
     * @return void
     **/
    public function testInvalid4(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Parser::parse('0.0.0-!@#');
    }

    /**
     * Test invalid version
     *
     * @return void
     **/
    public function testInvalid5(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Parser::parse('0.0.0-build.1+!@#');
    }
}
