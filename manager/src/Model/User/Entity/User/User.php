<?php

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;

class User
{
    private Id $id;
    private \DateTimeImmutable $date;
    private ?Email $email = null;
    private ?string $passwordHash;
    private ?string $confirmToken;
    private ?ResetToken $resetToken = null;
    private Status $status;
    private Role $role;
    private ArrayCollection $networks;

    #[Pure] private function __construct(Id $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function signUpByEmail(Id $id, \DateTimeImmutable $date, Email $email, string $hash, string $token): self
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = Status::wait();

        return $user;
    }

    public function confirmSignUp(): void
    {
        if (!$this->getStatus()->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = Status::active();
        $this->confirmToken = null;
    }

    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date, string $network, string $identity): self
    {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = Status::active();

        return $user;
    }

    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->getStatus()->isActive()) {
            throw new \DomainException('User is not active.');
        }

        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }

        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requested.');
        }

        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $date, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }

        if ($this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Reset token is expired.');
        }

        $this->passwordHash = $hash;
        $this->resetToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new \DomainException('Role is already same.');
        }

        $this->role = $role;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getNetworks(): ArrayCollection
    {
        return $this->networks;
    }
}
