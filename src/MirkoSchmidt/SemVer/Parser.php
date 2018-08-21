<?php
/**
 * Parser.php
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 * @subpackage      Parser
 */

namespace MirkoSchmidt\SemVer;

use MirkoSchmidt\SemVer\Parser\Build as BuildParser;
use MirkoSchmidt\SemVer\Parser\PreRelease as PreReleaseParser;
use MirkoSchmidt\SemVer\Parser\Versionable as VersionableParser;

use MirkoSchmidt\SemVer\Regex;

use MirkoSchmidt\SemVer\Version as SemVerVersion;

use \InvalidArgumentException as InvalidArgumentException;

/**
 * Parser
 *
 * Full SemVer version parser
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 * @subpackage      Parser
 */
class Parser
{
    /**
     * Parse a string into a SemVer Version
     *
     * @throws InvalidArgumentException
     *
     * @param string $string
     * @return Version\Versionable
     **/
    public static function parse($string)
    {
        $matches = Regex::matchSemVer($string);

        // Parse the SemVer root
        $version = VersionableParser::parse(
            $matches[1],
            'MirkoSchmidt\SemVer\Version'
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
