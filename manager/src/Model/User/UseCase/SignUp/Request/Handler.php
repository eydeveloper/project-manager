<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\SignUpConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $passwordHasher;
    private SignUpConfirmTokenizer $tokenizer;
    private SignUpConfirmTokenSender $sender;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $passwordHasher,
        SignUpConfirmTokenizer $tokenizer,
        SignUpConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->passwordHasher = $passwordHasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    /**
     * Метод отправки запроса на подтверждение регистрации.
     *
     * @param Command $command
     * @throws NonUniqueResultException|NoResultException
     */
    public function handle(Command $command): void
    {
        $email = new Email($command->getEmail());

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $email,
            $this->passwordHasher->hash($command->getPassword()),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);

        $this->sender->send($email, $token);

        $this->flusher->flush();
    }
}
