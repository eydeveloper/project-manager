<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Неверный формат электронной почты.');
        }

        $this->value = (string)mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[Pure] public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}
