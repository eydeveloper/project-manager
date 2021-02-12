<?php

namespace App\Security;

use App\Model\User\Entity\User\Status;
use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $status;
    private string $role;

    public function __construct(
        string $id,
        string $username,
        string $password,
        string $status,
        string $role
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->status = $status;
        $this->role = $role;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isActive(): string
    {
        return $this->status === Status::active()->getName();
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}