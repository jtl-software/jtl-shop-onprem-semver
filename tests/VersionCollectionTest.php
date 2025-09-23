<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Tests;

use JTLShop\SemVer\Parser;
use JTLShop\SemVer\VersionCollection;
use PHPUnit\Framework\TestCase;

class VersionCollectionTest extends TestCase
{
    public function testAppendAndNext(): void
    {
        $v1         = Parser::parse('1.0.0');
        $v2         = Parser::parse('1.1.0');
        $v3         = Parser::parse('1.1.1');
        $collection = new VersionCollection();
        $collection->append('1.0.0');
        $collection->append('1.1.0');
        $collection->append($v3);
        $this->assertEquals($v2, $collection->getNextVersion($v1));
        $this->assertEquals($v3, $collection->getNextVersion($v2));
        $this->assertNull($collection->getNextVersion($v3));
    }

    public function testAppendAndNextContruct(): void
    {
        $v1         = Parser::parse('2.0.0');
        $v2         = Parser::parse('2.1.0');
        $v3         = Parser::parse('2.1.1');
        $collection = new VersionCollection([$v1, $v2, $v3]);
        $this->assertEquals($v2, $collection->getNextVersion($v1));
        $this->assertEquals($v3, $collection->getNextVersion($v2));
        $this->assertNull($collection->getNextVersion($v3));
    }

    public function testVersionExists(): void
    {
        $v1         = Parser::parse('2.0.0');
        $v2         = Parser::parse('2.1.0');
        $v3         = Parser::parse('2.1.1');
        $collection = new VersionCollection([$v1, $v2, $v3]);
        $this->assertTrue($collection->versionExists('2.0.0'));
        $this->assertFalse($collection->versionExists('3.0.0'));
    }

    public function testVersionRange(): void
    {
        $v0         = Parser::parse('1.0.0');
        $v1         = Parser::parse('2.0.0');
        $v2         = Parser::parse('2.1.0');
        $v2b        = Parser::parse('2.1.0-beta.1');
        $v3         = Parser::parse('2.1.1');
        $v4         = Parser::parse('3.0.0');
        $collection = new VersionCollection([$v0, $v1, $v2, $v2b, $v3, $v4]);
        $range      = $collection->getVersionRange($v1, $v3);
        $this->assertTrue($range->versionExists($v2));
        $this->assertTrue($range->versionExists($v2b));
        $this->assertFalse($range->versionExists($v0));
        $this->assertFalse($range->versionExists($v4));
    }

    public function testGetBuilds(): void
    {
        $v0         = Parser::parse('1.1.0');
        $v1         = Parser::parse('1.1.1');
        $v2         = Parser::parse('1.1.2');
        $v2b        = Parser::parse('1.1.3-beta.1');
        $v3         = Parser::parse('1.2.1');
        $v4         = Parser::parse('2.0.0');
        $collection = new VersionCollection([$v0, $v1, $v2, $v2b, $v3, $v4]);
        $range      = $collection->getBuilds($v1);
        $this->assertTrue($range->versionExists($v1));
        $this->assertTrue($range->versionExists($v2));
        $this->assertTrue($range->versionExists($v2b));
        $this->assertFalse($range->versionExists($v3));
        $this->assertFalse($range->versionExists($v4));
    }

    public function testGetMinors(): void
    {
        $v0         = Parser::parse('1.1.0');
        $v1         = Parser::parse('1.1.1');
        $v2         = Parser::parse('1.1.2');
        $v2b        = Parser::parse('1.1.3-beta.1');
        $v3         = Parser::parse('1.2.1');
        $v4         = Parser::parse('2.0.0');
        $collection = new VersionCollection([$v0, $v1, $v2, $v2b, $v3, $v4]);
        $range      = $collection->getMinors($v1);
        $this->assertTrue($range->versionExists($v1));
        $this->assertTrue($range->versionExists($v2));
        $this->assertTrue($range->versionExists($v2b));
        $this->assertTrue($range->versionExists($v3));
        $this->assertFalse($range->versionExists($v4));
    }

    public function testGetLatestBuild(): void
    {
        $v0         = Parser::parse('1.1.0');
        $v1         = Parser::parse('1.1.1');
        $v2         = Parser::parse('1.1.2');
        $collection = new VersionCollection([$v0, $v1, $v2]);
        $this->assertSame($v2, $collection->getLatestBuild($v0));
        $this->assertSame($v2, $collection->getLatestBuild($v1));
    }
}
