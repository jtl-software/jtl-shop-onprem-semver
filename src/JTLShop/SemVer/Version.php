<?php

declare(strict_types=1);

namespace JTLShop\SemVer;

use InvalidArgumentException;
use JTLShop\SemVer\Version\Build;
use JTLShop\SemVer\Version\PreRelease;
use JTLShop\SemVer\Version\Versionable;

/**
 * @package JTLShop\SemVer
 */
class Version extends Versionable
{
    private ?PreRelease $preRelease = null;

    private ?Build $build = null;

    private string $originalVersionString = '';

    public function setOriginalVersion(string $version): self
    {
        $this->originalVersionString = $version;

        return $this;
    }

    public function getOriginalVersion(): string
    {
        return $this->originalVersionString;
    }

    public function getPreRelease(): PreRelease
    {
        if ($this->preRelease === null) {
            throw new InvalidArgumentException('PreRelease not set');
        }
        return $this->preRelease;
    }

    public function setPreRelease(PreRelease $preRelease): self
    {
        $this->preRelease = $preRelease;

        return $this;
    }

    public function hasPreRelease(): bool
    {
        return ($this->preRelease instanceof PreRelease);
    }

    public function getBuild(): Build
    {
        if ($this->build === null) {
            throw new InvalidArgumentException('Build not set');
        }
        return $this->build;
    }

    public function setBuild(Build $build): self
    {
        $this->build = $build;

        return $this;
    }

    public function hasBuild(): bool
    {
        return $this->build instanceof Build;
    }

    /**
     * Get the next logical version relative to the provided base version. If
     * no base is supplied, base will be the same as the current version.
     *
     * @throws InvalidArgumentException
     */
    public function next(mixed $base = null): self
    {
        //  Ensure that $base is a Version. Parse it if we must, use ourself if
        //  it is empty.
        if (empty($base)) {
            $base = $this;
        } elseif (\is_string($base)) {
            $base = Parser::parse($base);
        } elseif (!$base instanceof self) {
            throw new InvalidArgumentException('$base must be of type Version');
        }

        // If the base is ahead of this Version then the next version will be
        // the base.
        if (Compare::greaterThan($base, $this)) {
            return $base->cleanCopy();
        }

        $next = new self();

        $next->setPrefix($this->getPrefix());
        $next->setMajor($this->getMajor());
        $next->setMinor($this->getMinor());
        $next->setPatch($this->getPatch());

        if ($base->hasPreRelease()) {
            if ($this->hasPreRelease()) {
                // We already know that $base is less than or equal to $this
                // and we won't be jumping to the next greek value. So it is
                // safe use $this prerelease and just increment the release
                // number.
                $preRelease = $this->getPreRelease();
                $pre        = new PreRelease();
                if (!empty($preRelease->getGreek())) {
                    $pre->setGreek($preRelease->getGreek());
                    $pre->setReleaseNumber($preRelease->getReleaseNumber() + 1);
                } else {
                    $pre->setPatch($preRelease->getPatch() + 1);
                }

                $next->setPreRelease($pre);
            } else {
                throw new InvalidArgumentException(
                    'This version has left prerelease without updating the base. Base should not be prerelease.'
                );
            }
        } elseif (!$this->hasPreRelease()) {
            $next->setPatch($this->getPatch() + 1);
        }
        // The case of $this having a pre-release when $base does not means
        // that we are essentially just leaving pre-release. Nothing needs to
        // be done.

        return $next;
    }

    /**
     * Create a new Version that discards the entity information of build and
     * originalVersionString
     */
    public function cleanCopy(): self
    {
        $version = new self();

        $version->setPrefix($this->getPrefix());
        $version->setMajor($this->getMajor());
        $version->setMinor($this->getMinor());
        $version->setPatch($this->getPatch());
        if ($this->hasPreRelease()) {
            $version->preRelease = clone($this->getPreRelease());
        }

        return $version;
    }

    public function __toString(): string
    {
        $string = parent::__toString();

        // Add pre-release
        if ($this->hasPreRelease()) {
            $string .= '-' . $this->getPreRelease();
        }

        // Add build
        if ($this->hasBuild()) {
            $string .= '+' . $this->getBuild();
        }

        return $string;
    }

    /**
     * Parse a new version or return an existing version
     */
    public static function parse(int|string|Version $version): self
    {
        if ($version instanceof self) {
            return $version;
        }

        return Parser::parse($version);
    }

    /**
     * Check if the version is greater than another version
     */
    public function greaterThan(int|string|Version $v2): bool
    {
        return Compare::greaterThan($this, self::parse($v2));
    }

    /**
     * Check if the version is smaller than another version
     */
    public function smallerThan(int|string|Version $v2): bool
    {
        return Compare::smallerThan($this, self::parse($v2));
    }

    /**
     * Check if the version is equals to another version
     */
    public function equals(int|string|Version $v2): bool
    {
        return Compare::equals($this, self::parse($v2));
    }
}
