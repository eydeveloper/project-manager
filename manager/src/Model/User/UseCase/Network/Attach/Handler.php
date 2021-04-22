<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
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
     * Метод привязки социальной сети к аккаунту пользователя.
     *
     * @param Command $command
     * @throws NonUniqueResultException|NoResultException
     */
    public function handle(Command $command): void
    {
        if ($this->users->hasByNetworkIdentity(
            $network = $command->getNetwork(),
            $identity = $command->getIdentity()
        )) {
            throw new \DomainException('Социальная сеть уже привязана.');
        }

        $user = $this->users->get(new Id($command->getUser()));

        $user->attachNetwork($network, $identity);

        $this->flusher->flush();
    }
}
