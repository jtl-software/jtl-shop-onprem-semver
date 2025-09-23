<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Parser;

use InvalidArgumentException;
use JTLShop\SemVer\Version;
use JTLShop\SemVer\Version\PreRelease;

/**
 * Parses strings in form X.Y.Z into major, minor, patch of Versionable
 *
 * @package JTLShop\SemVer\Parser
 */
class Versionable
{
    /**
     * Parse a string into Versionable properties
     *
     * @param class-string<Version\Versionable> $className
     * @throws InvalidArgumentException
     */
    public static function parse(string $string, string $className): PreRelease|Version
    {
        // Sanity check
        if (\substr_count($string, '.') < 1 || \substr_count($string, '.') > 2) {
            throw new InvalidArgumentException(
                \sprintf('part "%s" can not be parsed into a SemVer major.minor.patch version', $string)
            );
        }
        // Simple explode will do here
        $parts = \explode('.', $string);
        // Instantiate
        $versionable = new $className();
        // Sanity check of class type
        if (!($versionable instanceof Version || $versionable instanceof PreRelease)) {
            throw new InvalidArgumentException(\sprintf('"%s" is not Version', $className));
        }

        // Set prefix, if it exists
        \preg_match('/^([a-z])?([\d]+)$/', $parts[0], $m);
        $prefix = !empty($m[1]) ? $m[1] : null;
        $build  = $m[2];
        // Check if patch exists
        if (isset($parts[2])) {
            $versionable->setPatch((int)$parts[2]);
        } else {
            $versionable->setSelectLastPatch(true);
        }
        // Versionable parts
        $versionable->setPrefix($prefix)
            ->setMajor((int)$build)
            ->setMinor((int)$parts[1]);

        return $versionable;
    }
}
