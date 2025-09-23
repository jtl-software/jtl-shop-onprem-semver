<?php

declare(strict_types=1);

namespace JTLShop\SemVer;

use InvalidArgumentException;
use JTLShop\SemVer\Version\Build;
use JTLShop\SemVer\Version\PreRelease;
use JTLShop\SemVer\Version\Versionable;

/**
 * Comparisons and functions around SemVer versions
 *
 * @package JTLShop\SemVer
 */
class Compare
{
    /**
     * Greek part precedence
     *
     * @var string[]
     */
    private static array $greekPrecendence = [
        'pre-alpha',
        'alpha',
        'pre-beta',
        'beta',
        'pre-rc',
        'rc'
    ];

    /**
     * Get the greatest SemVer out of a set of Versions
     *
     * @throws InvalidArgumentException
     */
    public static function greatest(): Version
    {
        /** @var array<int, Version|Version[]> $args */
        $args = \func_get_args();
        // Number of versions given
        $count = \count($args);
        // Make sure there's one
        if ($count === 0) {
            throw new InvalidArgumentException('No versions given');
        }
        /** @var array<int, Version> $versions */
        $versions = \is_array($args[0]) ? $args[0] : $args;
        $count    = \count($versions);
        // If there's only one, it is great. Very great.
        if ($count === 1) {
            return $versions[0];
        }
        /** @var Version $version1 */
        $version1 = \array_shift($versions);
        /** @var Version $version2 */
        $version2 = \array_shift($versions);

        $greatest = $version2;
        if (self::greaterThan($version1, $version2)) {
            $greatest = $version1;
        }
        // Recurse if there's versions left
        if (\count($versions) > 0) {
            return self::greatest(\array_merge([$greatest], $versions));
        }

        return $greatest;
    }

    public static function equals(Version $version1, Version $version2): bool
    {
        if (
            !self::versionableEquals($version1, $version2) // Versionable part itself needs to be equal
            || ($version1->hasPreRelease() && !$version2->hasPreRelease()) // If only one has a pre-release - not equal
            || (!$version1->hasPreRelease() && $version2->hasPreRelease())
        ) {
            return false;
        }
        // See if pre-releases are equal if both versions have one
        if (
            $version1->hasPreRelease()
            && $version2->hasPreRelease()
            && !self::preReleaseEquals($version1->getPreRelease(), $version2->getPreRelease())
        ) {
            return false;
        }
        // If only one has a build version, they're not equal
        if (
            (!$version1->hasBuild() && $version2->hasBuild())
            || ($version1->hasBuild() && !$version2->hasBuild())
        ) {
            return false;
        }
        // Compare the build version
        if ($version1->hasBuild() && $version2->hasBuild()) {
            return self::buildEquals($version1->getBuild(), $version2->getBuild());
        }

        return true;
    }

    public static function greaterThan(Version $version1, Version $version2): bool
    {
        // If they are equal, they can not be greater/smaller than each other
        if (self::equals($version1, $version2)) {
            return false;
        }
        // Compare on the major/minor/patch level if we can
        if (!self::versionableEquals($version1, $version2)) {
            return self::versionableGreaterThan($version1, $version2);
        }
        if (($check = self::checkPreReleaseVersion($version1, $version2)) !== null) {
            return $check;
        }
        if (!$version1->hasBuild() && $version2->hasBuild()) {
            return false;
        }

        return true;
    }

    public static function smallerThan(Version $version1, Version $version2): bool
    {
        // If it's equal it cannot be smaller
        if (self::equals($version1, $version2)) {
            return false;
        }
        // If it's not equal, and not greater, it must be smaller
        return !self::greaterThan($version1, $version2);
    }

    private static function versionableGreaterThan(Versionable $version1, Versionable $version2): bool
    {
        if ($version1->getMajor() > $version2->getMajor()) {
            return true;
        }
        if ($version1->getMajor() < $version2->getMajor()) {
            return false;
        }
        if ($version1->getMinor() > $version2->getMinor()) {
            return true;
        }
        if ($version1->getMinor() < $version2->getMinor()) {
            return false;
        }

        return $version1->getPatch() > $version2->getPatch();
    }

    private static function versionableEquals(Versionable $version1, Versionable $version2): bool
    {
        if ($version1->getMajor() !== $version2->getMajor()) {
            return false;
        }
        if ($version1->getMinor() !== $version2->getMinor()) {
            return false;
        }

        return $version1->getPatch() === $version2->getPatch();
    }

    private static function buildEquals(Build $version1, Build $version2): bool
    {
        return $version1->getNumber() === $version2->getNumber();
    }

    private static function buildGreaterThan(Build $version1, Build $version2): bool
    {
        return $version1->getNumber() > $version2->getNumber();
    }

    private static function preReleaseEquals(PreRelease $version1, PreRelease $version2): bool
    {
        // If they don't match on the versionable level they can not match
        if (!self::versionableEquals($version1, $version2)) {
            return false;
        }
        // Both other parts need to match
        if ($version1->getGreek() !== $version2->getGreek()) {
            return false;
        }
        if ($version1->getReleaseNumber() !== $version2->getReleaseNumber()) {
            return false;
        }

        return true;
    }

    private static function checkPreReleaseVersion(Version $version1, Version $version2): ?bool
    {
        // v1 has a pre-release, but v2 does not, v2 is bigger
        if ($version1->hasPreRelease() && !$version2->hasPreRelease()) {
            return false;
        }
        // v1 does not have a pre-release, but v2 does, v1 is bigger
        if (!$version1->hasPreRelease() && $version2->hasPreRelease()) {
            return true;
        }
        // Compare on the re-release level if possible
        if (
            $version1->hasPreRelease()
            && $version2->hasPreRelease()
            && !self::preReleaseEquals($version1->getPreRelease(), $version2->getPreRelease())
        ) {
            // If v1 has a larger pre-release than v2, it's bigger
            return self::preReleaseGreaterThan($version1->getPreRelease(), $version2->getPreRelease());
        }
        // Compare the build version
        if ($version1->hasBuild() && $version2->hasBuild()) {
            return self::buildGreaterThan($version1->getBuild(), $version2->getBuild());
        }

        return null;
    }

    private static function preReleaseGreaterThan(PreRelease $version1, PreRelease $version2): bool
    {
        // Pre-releases can be denoted as versionable (X.Y.Z) or greek (alpha.5)
        // We let versionable take precedence
        if (!self::versionableEquals($version1, $version2)) {
            // If v1 is bigger on the versionable level, it's bigger.
            return self::versionableGreaterThan($version1, $version2);
        }
        // "Greek" part is "bigger" for v1
        $v1greek = $version1->getGreek();
        $v2greek = $version2->getGreek();
        if ($v1greek !== $v2greek && $v1greek !== null && $v2greek !== null) {
            return self::greekLargerThan($v1greek, $v2greek);
        }
        // Release number for v1 is bigger than v2's
        if ($version1->getReleaseNumber() > $version2->getReleaseNumber()) {
            return true;
        }

        return false;
    }

    private static function greekLargerThan(string $greek1, string $greek2): bool
    {
        // If they do not exist as valid precendence identifiers assume
        // bigger/smaller based on that
        if (!\in_array($greek1, self::$greekPrecendence, true)) {
            return true;
        }
        if (!\in_array($greek2, self::$greekPrecendence, true)) {
            return false;
        }
        // Use the index in precendence for comparison
        return \array_search($greek1, self::$greekPrecendence, true)
            > \array_search($greek2, self::$greekPrecendence, true);
    }
}
