<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Status
{
    private const WAIT = 'STATUS_WAIT';
    private const ACTIVE = 'STATUS_ACTIVE';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::WAIT,
            self::ACTIVE,
        ]);

        $this->name = $name;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    #[Pure]
    public function isWait(): bool
    {
        return $this->getName() === self::WAIT;
    }

    #[Pure]
    public function isActive(): bool
    {
        return $this->getName() === self::ACTIVE;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
