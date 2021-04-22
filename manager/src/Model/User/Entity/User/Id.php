<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    /**
     * @var string
     */
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    /**
     * Метод возвращает новый уникальный идентификатор.
     *
     * @return Id
     */
    public static function next(): Id
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * Метод возвращает идентификатор в виде строки.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}