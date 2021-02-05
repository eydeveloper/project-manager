<?php

namespace App\Model\User\Entity\User;

use DateTimeImmutable;

class User
{
    /**
     * @var Id
     */
    private Id $id;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $date;
    /**
     * @var Email
     */
    private Email $email;
    /**
     * @var string
     */
    private string $passwordHash;

    public function __construct(Id $id, DateTimeImmutable $date, Email $email, string $passwordHash)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
