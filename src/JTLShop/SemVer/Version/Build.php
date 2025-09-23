<?php

declare(strict_types=1);

namespace JTLShop\SemVer\Version;

use InvalidArgumentException;

/**
 * @package JTLShop\SemVer\Version
 */
class Build
{
    /**
     * Build number
     */
    private int $number = 0;

    /**
     * @var array<int, string>
     */
    private array $parts = [];

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        if ($number < 0) {
            throw new InvalidArgumentException(\sprintf('Build number "%s" is invalid', $number));
        }
        $this->number = $number;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param array<int, string> $parts
     */
    public function setParts(array $parts): self
    {
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    public function addPart(string $part): self
    {
        if (!\ctype_alnum($part)) {
            throw new InvalidArgumentException(\sprintf('Build part "%s" is not alpha numerical', $part));
        }
        $this->parts[] = $part;

        return $this;
    }

    public function __toString(): string
    {
        // If there are other parts
        if (\count($this->getParts()) > 0) {
            $parts = ['build'];
            // Add number if we have one
            $parts[] = $this->getNumber();
            $parts[] = \implode('.', $this->getParts());

            return \implode('.', $parts);
        }
        // No number, no parts, no output.
        if ($this->getNumber() === 0) {
            return '';
        }

        return 'build.' . $this->getNumber();
    }
}
