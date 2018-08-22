<?php
/**
 * VersionRange.php
 *
 * @category        JTLShop
 * @package         SemVer
 */

namespace JTLShop\SemVer;

use JTLShop\SemVer\Version\Versionable;

/**
 * Sort
 *
 * Sorting sets of SemVer Versions
 *
 * @category        JTLShop
 * @package         SemVer
 */
class VersionRange
{
    /**
     * @var array $versions
     */
    private $versions;

    /**
     * @var array $versionRange
     */
    private $versionRange;

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param array $versions
     *
     * @return VersionRange
     */
    public function setVersions(array $versions)
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * @return array
     */
    public function getVersionRange()
    {
        return $this->versionRange;
    }

    /**
     * VersionRange constructor.
     *
     * @param array $versions
     */
    public function __construct(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * @param Versionable $startVersion
     * @param Versionable $targetVersion
     *
     * @return void
     */
    public function setVersionRange(Versionable $startVersion, Versionable $targetVersion)
    {
        $versionRange = [];
        $versionRange[] = $startVersion;

        $versions = Sort::sortArray($this->versions);

        foreach ($versions as $version) {
            if (Compare::greaterThan($version, $startVersion) && Compare::smallerThan($version, $targetVersion)) {
                $versionRange[] = $version;
            }
        }

        $versionRange[] = $targetVersion;

        $this->versionRange = Sort::sortArray($versionRange);
    }

    /**
     * @param $currentVersion
     *
     * @return Versionable|null
     */
    public function getNextVersion($currentVersion)
    {
        foreach ($this->versionRange as $key => $version) {
            if (Compare::equals($currentVersion, $version)) {
                return isset($this->versionRange[($key + 1)]) ? $this->versionRange[($key + 1)] : null;
            }
        }

        return null;
    }
}
