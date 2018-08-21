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
        if (substr_count($string, '.') < 1 && substr_count($string, '.') > 2) {
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

        // Set prefix, if it exists
        preg_match('/^([a-z])?([0-9])+$/', $parts[0], $m);
        $praefix = !empty($m[1]) ? $m[1] : null;
        $build   = $m[2];

        // Check if patch exists
        if (isset($parts[2])) {
            $versionable->setPatch((int) $parts[2]);
        } else {
            $versionable->setSelectLastPatch(true);
        }

        // Versionable parts
        $versionable
            ->setPrefix($praefix)
            ->setMajor((int) $build)
            ->setMinor((int) $parts[1]);

        return $versionable;
    }
}
