<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\User\Status;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
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

    public function isActive(): bool
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

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->status === $user->status &&
            $this->role === $user->role;
    }
}