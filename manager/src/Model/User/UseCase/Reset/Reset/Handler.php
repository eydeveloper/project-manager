<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    /**
     * Метод восстановления пароля.
     *
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByResetToken($command->getToken())) {
            throw new \DomainException('Некорректный или уже подтвержденный токен.');
        }

        $user->passwordReset(
            new \DateTimeImmutable(),
            $this->hasher->hash($command->getPassword())
        );

        $this->flusher->flush();
    }
}
