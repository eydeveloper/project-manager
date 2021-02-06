<?php

namespace App\Model\User\UseCase\Reset;

use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\ResetTokenizer;
use App\Model\User\Service\ResetTokenSender;

class Handler
{
    private UserRepository $users;
    private ResetTokenizer $tokenizer;
    private ResetTokenSender $sender;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        ResetTokenizer $tokenizer,
        ResetTokenSender $sender,
        Flusher $flusher,
    )
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handler(Command $command): void
    {
        $user = $this->users->getByEmail($command->email);

        $user->requestPasswordReset(
            $this->tokenizer->generate(),
            new \DateTimeImmutable()
        );

        $this->flusher->flush();

        $this->sender->send($user->getEmail(), $user->getResetToken());
    }
}
