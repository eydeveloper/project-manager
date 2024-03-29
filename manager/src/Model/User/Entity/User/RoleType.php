<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use JetBrains\PhpStorm\Pure;

class RoleType extends StringType
{
    public const NAME = 'user_user_role';

    /**
     * {@inheritdoc}
     */
    #[Pure]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof Role ? $value->getName() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Role
    {
        return !empty($value) ? new Role($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
