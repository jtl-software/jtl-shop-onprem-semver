<?php

declare(strict_types=1);

namespace JTLShop\SemVer;

/**
 * Sorting sets of SemVer Versions
 *
 * @package JTLShop\SemVer
 * @extends \ArrayIterator<int, Version>
 */
class VersionCollection extends \ArrayIterator
{
    /**
     * @param array<Version|string|int|\stdClass> $versionArr
     */
    public function __construct(array $versionArr = [], int $flags = 0)
    {
        $versions = [];
        foreach ($versionArr as $version) {
            /** @var string|int|Version $parsed */
            /** @noinspection MissingIssetImplementationInspection */
            $parsed     = \is_object($version)
            && \is_a($version, \stdClass::class)
            && isset($version->reference)
                ? $version->reference
                : $version;
            $versions[] = Version::parse($parsed);
        }

        parent::__construct($versions, $flags);
    }

    /**
     * @param Version|string|int $value
     */
    public function append($value): void
    {
        parent::append(Version::parse($value));
    }

    /**
     * @param int|string|Version $version
     * @return mixed|null
     */
    public function getLatestBuild(int|string|Version $version): mixed
    {
        $version            = Version::parse($version);
        $latestBuildVersion = null;
        $relatedVersions    = $this->getBuilds($version);

        foreach ($relatedVersions as $relatedVersion) {
            if (empty($latestBuildVersion)) {
                $latestBuildVersion = $relatedVersion;
            } elseif ($relatedVersion->greaterThan($latestBuildVersion)) {
                $latestBuildVersion = $relatedVersion;
            }
        }

        return $latestBuildVersion;
    }

    /**
     * @return VersionCollection<int, Version>
     */
    public function getMinors(int|string|Version $version): self
    {
        $version         = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }

    /**
     * @return VersionCollection<int, Version>
     */
    public function getBuilds(int|string|Version $version): self
    {
        $version         = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor() && $vcVersion->getMinor() === $version->getMinor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }

    /**
     * @return VersionCollection<int, Version>
     */
    public function getVersionRange(int|string|Version $start, int|string|Version $end): self
    {
        $startKey = 0;
        $endKey   = 0;
        foreach ($this as $key => $version) {
            if ($version->equals(Version::parse($start))) {
                $startKey = $key;
            }
            if ($version->equals(Version::parse($end))) {
                $endKey = $key;
            }
        }

        if ($startKey === $endKey) {
            return new self();
        }
        $arr = [];
        foreach ($this as $key => $version) {
            if ($key >= $startKey && $key <= $endKey) {
                $arr[] = $version;
            }
        }

        return new self($arr);
    }

    public function versionExists(int|string|Version $version): bool
    {
        $exists = false;
        $copy   = $this->getArrayCopy();
        \array_walk($copy, static function (Version $item) use (&$exists, $version) {
            if ($item->equals($version)) {
                $exists = true;
            }
        });

        return $exists;
    }

    public function getNextVersion(int|string|Version $currentVersion): ?Version
    {
        foreach ($this as $key => $version) {
            if ($version->equals($currentVersion)) {
                return $this[($key + 1)] ?? null;
            }
        }

        return null;
    }
}
