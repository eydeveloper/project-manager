<?php

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $passwordHasher;
    private SignUpConfirmTokenizer $tokenizer;
    private ConfirmTokenSender $sender;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        PasswordHasher $passwordHasher,
        SignUpConfirmTokenizer $tokenizer,
        ConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->users = $users;
        $this->passwordHasher = $passwordHasher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByEmail(
            $email,
            $this->passwordHasher->hash($command->password),
            $token = $this->tokenizer->generate(),
        );

        $this->users->add($user);

        $this->sender->send($email, $token);

        $this->flusher->flush();
    }
}
