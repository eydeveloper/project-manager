<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Ramsey\Uuid\Uuid;

class SignUpConfirmTokenizer
{
    /**
     * Метод генерирует и возвращает токен подтверждения регистрации пользователя.
     *
     * @return string
     */
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
