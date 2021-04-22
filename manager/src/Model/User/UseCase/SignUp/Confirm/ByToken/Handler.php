<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm\ByToken;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    /**
     * Метод подтверждения регистрации пользователя по токену.
     *
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByConfirmToken($command->getToken())) {
            throw new \DomainException('Некорректный или уже подтвержденный токен.');
        }

        $user->confirmSignUp();

        $this->flusher->flush();
    }
}
