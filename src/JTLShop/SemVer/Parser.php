<?php

declare(strict_types=1);

namespace JTLShop\SemVer;

use InvalidArgumentException;
use JTLShop\SemVer\Parser\Build as BuildParser;
use JTLShop\SemVer\Parser\PreRelease as PreReleaseParser;
use JTLShop\SemVer\Parser\Versionable as VersionableParser;
use JTLShop\SemVer\Version\PreRelease;

/**
 * @package JTLShop\SemVer
 */
class Parser
{
    /**
     * Parse a string into a SemVer Version
     *
     * @throws InvalidArgumentException
     */
    public static function parse(int|string $string): Version
    {
        $string  = (string)$string;
        $matches = Regex::matchSemVer($string);
        /** @var Version $version */
        $version = VersionableParser::parse($matches[1], Version::class);
        // There is a pre-release part
        if (!empty($matches['prerelease'])) {
            /** @var PreRelease $prerelease */
            $prerelease = PreReleaseParser::parse(\ltrim($matches['prerelease'], '-'));
            $version->setPreRelease($prerelease);
        }
        // There is a build number
        if (!empty($matches['build'])) {
            $version->setBuild(BuildParser::parse(\ltrim($matches['build'], '+')));
        }
        $version->setOriginalVersion($string);

        return $version;
    }
}
