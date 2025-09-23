<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Version;

/**
 * @package JTLShop\SemVer\Version
 */
class PreRelease extends Versionable
{
    private ?string $greek = null;

    private int $releaseNumber = 0;

    public function setGreek(string $greek): self
    {
        $this->greek = $greek;

        return $this;
    }

    public function setReleaseNumber(int $releaseNumber): self
    {
        $this->releaseNumber = $releaseNumber;

        return $this;
    }

    public function getGreek(): ?string
    {
        return $this->greek;
    }

    public function getReleaseNumber(): int
    {
        return $this->releaseNumber;
    }

    public function __toString(): string
    {
        if ($this->getMajor() > 0 || $this->getMinor() > 0 || $this->getPatch() > 0) {
            return parent::__toString();
        }
        $preReleaseStr = $this->getGreek();
        if ($this->getReleaseNumber() > 0) {
            $preReleaseStr .= '.' . $this->getReleaseNumber();
        }

        return $preReleaseStr ?? '';
    }
}
