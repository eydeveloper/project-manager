<?php

declare(strict_types=1);

namespace App\Model\User\Service;

class PasswordHasher
{
    /**
     * Метод генерирует и возвращает хеш пароля пользователя.
     *
     * @param string $password
     * @return string|null
     */
    public function hash(string $password): ?string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    /**
     * Метод возвращает результат проверки соответствия входящего пароля с захешированным паролем.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
