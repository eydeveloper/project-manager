<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

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
     * Метод регистрации пользователя по социальной сети.
     *
     * @param Command $command
     * @throws NoResultException|NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentity(
            $network = $command->getNetwork(),
            $identity = $command->getIdentity()
        )) {
            throw new \DomainException('Не удалось зарегистрировать пользователя.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $network,
            $identity
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}
