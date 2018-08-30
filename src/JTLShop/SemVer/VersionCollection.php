<?php
/**
 * VersionRange.php
 *
 * @category        JTLShop
 * @package         SemVer
 */

namespace JTLShop\SemVer;


/**
 * Sort
 *
 * Sorting sets of SemVer Versions
 *
 * @category        JTLShop
 * @package         SemVer
 */
class VersionCollection extends \ArrayIterator
{
    /**
     * VersionCollection constructor.
     *
     * @param array $versionArr
     * @param int $flags
     */
    public function __construct(array $versionArr = [], $flags = 0)
    {
        $versions = [];

        foreach ($versionArr as $version) {
            try {
                $versions[] = Version::parse($version);
            } catch (\Exception $e) {
                $versions[] = Version::parse($version->reference);
            }
        }

        parent::__construct($versions, $flags);
    }

    /**
     * @param Version|string $version
     *
     * @return mixed|null
     */
    public function getLatestBuild($version)
    {
        $version = Version::parse($version);
        $latestBuildVersion = null;
        $relatedVersions = $this->getBuilds($version);

        foreach ($relatedVersions as $relatedVersion) {
            if (empty($latestBuildVersion)) {
                $latestBuildVersion = $relatedVersion;
            } else {
                if ($relatedVersion->greaterThan($latestBuildVersion)) {
                    $latestBuildVersion = $relatedVersion;
                }
            }
        }

        return $latestBuildVersion;
    }

    /**
     * @param Version|string $version
     *
     * @return VersionCollection
     */
    public function getMinors($version)
    {
        $version = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }

    /**
     * @param Version|string $version
     *
     * @return VersionCollection
     */
    public function getBuilds($version)
    {
        $version = Version::parse($version);
        $relatedVersions = [];
        foreach ($this as $vcVersion) {
            if ($vcVersion->getMajor() === $version->getMajor() && $vcVersion->getMinor() === $version->getMinor()) {
                $relatedVersions[] = $vcVersion;
            }
        }

        return new self($relatedVersions);
    }
}
