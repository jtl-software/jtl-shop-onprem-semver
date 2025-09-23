<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Version;

/**
 * @package JTLShop\SemVer\Version
 */
abstract class Versionable
{
    private ?string $prefix = null;

    private int $major = 0;

    private int $minor = 0;

    private int $patch = 0;

    private bool $selectLastPatch = false;

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function setMajor(int $major): self
    {
        $this->major = $major;

        return $this;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function setMinor(int $minor): self
    {
        $this->minor = $minor;

        return $this;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function setPatch(int $patch): self
    {
        $this->patch = $patch;

        return $this;
    }

    public function isSelectLastPatch(): bool
    {
        return $this->selectLastPatch;
    }

    public function setSelectLastPatch(bool $selectLastPatch): self
    {
        $this->selectLastPatch = $selectLastPatch;

        return $this;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s%d.%d.%d',
            $this->getPrefix(),
            $this->getMajor(),
            $this->getMinor(),
            $this->getPatch()
        );
    }
}
