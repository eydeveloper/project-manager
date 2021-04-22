<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Exception\UserEmailAlreadyInUse;
use App\Model\User\Service\NewEmailTokenizer;
use App\Model\User\Service\NewEmailTokenSender;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class Handler
{
    private UserRepository $users;
    private NewEmailTokenizer $tokenizer;
    private NewEmailTokenSender $sender;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        NewEmailTokenizer $tokenizer,
        NewEmailTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    /**
     * Метод отправки запроса на смену электронной почты пользователя.
     *
     * @param Command $command
     * @throws NoResultException|NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->getId()));

        $email = new Email($command->getEmail());

        if ($this->users->hasByEmail($email)) {
            throw new UserEmailAlreadyInUse('Указанная электронная почта привязана к другому аккаунту.');
        }

        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate()
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
