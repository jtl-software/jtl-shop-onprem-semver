<?php
/**
 * Parser.php
 *
 * @category        Naneau
 * @package         SemVer
 * @subpackage      Parser
 */

namespace JTLShop\SemVer;

use JTLShop\SemVer\Parser\Build as BuildParser;
use JTLShop\SemVer\Parser\PreRelease as PreReleaseParser;
use JTLShop\SemVer\Parser\Versionable as VersionableParser;
use \InvalidArgumentException as InvalidArgumentException;

/**
 * Parser
 *
 * Full SemVer version parser
 *
 * @category        Naneau
 * @package         SemVer
 * @subpackage      Parser
 */
class Parser
{
    /**
     * Parse a string into a SemVer Version
     *
     * @param string $string
     *
     * @return Version
     * @throws InvalidArgumentException
     **/
    public static function parse($string)
    {
        $matches = Regex::matchSemVer($string);

        // Parse the SemVer root
        $version = VersionableParser::parse(
            $matches[1],
            'JTLShop\SemVer\Version'
        );

        // There is a pre-release part
        if (!empty($matches['prerelease'])) {
            $version->setPreRelease(
                PreReleaseParser::parse(
                    ltrim($matches['prerelease'], '-')
                )
            );
        }

        // There is a build number
        if (!empty($matches['build'])) {
            $version->setBuild(
                BuildParser::parse(
                    ltrim($matches['build'], '+')
                )
            );
        }

        // Return
        $version->setOriginalVersion($string);

        return $version;
    }
}
