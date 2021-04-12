<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::ADMIN,
        ]);

        $this->name = $name;
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    #[Pure] public function isUser(): bool
    {
        return $this->getName() === self::USER;
    }

    #[Pure] public function isAdmin(): bool
    {
        return $this->getName() === self::ADMIN;
    }

    #[Pure] public function isEqual(self $role): bool
    {
        return $this->getName() === $role->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
