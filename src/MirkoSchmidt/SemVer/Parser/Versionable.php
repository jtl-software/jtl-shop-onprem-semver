<?php
/**
 * Versionable.php
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 * @subpackage      Parser
 */

namespace MirkoSchmidt\SemVer\Parser;

use MirkoSchmidt\SemVer\Version\Versionable as VersionableVersion;

use \InvalidArgumentException as InvalidArgumentException;

/**
 * Versionable
 *
 * Parses strings in form X.Y.Z into major, minor, patch of Versionable
 *
 * @category        MirkoSchmidt
 * @package         SemVer
 * @subpackage      Parser
 */
class Versionable
{
    /**
     * Parse a string into Versionable properties
     *
     * @throws InvalidArgumentException
     *
     * @param  string             $string
     * @param  string             $className
     * @return VersionableVersion
     **/
    public static function parse($string, $className)
    {
        // Sanity check
        if (substr_count($string, '.') !== 2) {
            throw new InvalidArgumentException(
                'part "' . $string . '" can not be parsed into a SemVer'
                . ' major.minor.patch version'
            );
        }

        // Simple explode will do here
        $parts = explode('.', $string);

        // Instantiate
        $versionable = new $className;

        // Sanity check of class type
        if (!($versionable instanceof VersionableVersion)) {
            throw new InvalidArgumentException(
                '"' . $className . '" is not Versionable'
            );
        }

        preg_match('/^([a-z])?([0-9])+$/', $parts[0], $m);
        $praefix = $m[1];
        $build   = $m[2];

        // Versionable parts
        $versionable
            ->setPrefix($praefix)
            ->setMajor((int) $build)
            ->setMinor((int) $parts[1])
            ->setPatch((int) $parts[2]);

        return $versionable;
    }
}
