<?php

declare(strict_types=1);

namespace JTLShop\SemVer;

use InvalidArgumentException;

/**
 * @package JTLShop\SemVer
 */
class Sort
{
    /**
     * Sort a set of SemVer versions
     *
     * This method is a catch-all and accepts:
     *
     *  - A single array of strings or Versions
     *  - A variable number of strings and Versions
     *
     * Strings will be parsed into Version instances
     *
     * The result will be a sorted array of Versions, in ascending order
     *
     * @return Version[]
     * @throws InvalidArgumentException
     */
    public static function sort(): array
    {
        /** @var array<int, string|Version|array<string|Version>> $arguments */
        $arguments = \func_get_args();

        if (\count($arguments) === 0) {
            return [];
        }
        // If the first argument is an array, use that
        /** @var array<int, Version|string> $versions */
        $versions = \is_array($arguments[0]) ? $arguments[0] : $arguments;
        // Parse into Version instances
        foreach ($versions as $key => $version) {
            if ($version instanceof Version) {
                $versions[$key] = $version;
            } elseif (\is_string($version)) {
                $versions[$key] = Parser::parse($version);
            } else {
                throw new InvalidArgumentException('Invalid version given, pass either Version instances or strings');
            }
        }

        /** @var array<int, Version> $versions */
        return self::sortArray($versions);
    }

    /**
     * Sort an array of Versions
     *
     * unlike sort() this method accepts only an array of Version instances
     *
     * This method uses QuickSort for the actual sorting
     *
     * @param array<int, Version> $versions
     * @return Version[]
     */
    public static function sortArray(array $versions): array
    {
        // Empty array does not needs sorting
        if (\count($versions) === 0) {
            return [];
        }

        // Array of one item (pivot) from the middle
        $pivotArray = \array_splice(
            $versions,
            (int)\floor((\count($versions) - 1) / 2),
            1
        );
        // Smaller/greater than pivot stack
        $smaller = [];
        $greater = [];

        // Fill stacks
        foreach ($versions as $version) {
            if (Compare::greaterThan($version, $pivotArray[0])) {
                $greater[] = $version;
            } else {
                $smaller[] = $version;
            }
        }

        // Recurse and merge results
        return \array_merge(
            self::sortArray($smaller),
            $pivotArray,
            self::sortArray($greater)
        );
    }
}
