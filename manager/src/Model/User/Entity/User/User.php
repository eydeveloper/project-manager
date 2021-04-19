<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\User\Exception\UserAlreadySameEmail;
use App\Model\User\Exception\UserEmailChangingNotRequested;
use App\Model\User\Exception\UserInvalidNewEmailToken;
use App\Model\User\Exception\UserNotActiveException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_token"})
 * })
 */
class User
{
    /**
     * Идентификатор.
     *
     * @ORM\Column(type="user_user_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * Дата регистрации.
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * Электронная почта.
     *
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private ?Email $email = null;

    /**
     * Хеш пароля.
     *
     * @ORM\Column(type="string", nullable=true, name="password_hash")
     */
    private ?string $passwordHash;

    /**
     * Токен подтверждения электронной почты.
     *
     * @ORM\Column(type="string", nullable=true, name="confirm_token")
     */
    private ?string $confirmToken;

    /**
     * Новая электронная почта.
     *
     * @ORM\Column(type="user_user_email", nullable=true, name="new_email")
     */
    private ?Email $newEmail = null;

    /**
     * Токен изменения электронной почты.
     *
     * @ORM\Column(type="string", nullable=true, name="new_email_token")
     */
    private ?string $newEmailToken = null;

    /**
     * Токен восстановления пароля.
     *
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private ?ResetToken $resetToken = null;

    /**
     * Статус.
     *
     * @ORM\Column(type="user_user_status")
     */
    private Status $status;

    /**
     * Роль.
     *
     * @ORM\Column(type="user_user_role")
     */
    private Role $role;

    /**
     * Социальные сети.
     *
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private mixed $networks;

    private function __construct(Id $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    /**
     * Регистрация пользователя по электронной почте.
     *
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param Email $email
     * @param string $hash
     * @param string $token
     * @return static
     */
    public static function signUpByEmail(Id $id, \DateTimeImmutable $date, Email $email, string $hash, string $token): self
    {
        $user = new self($id, $date);
        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = Status::wait();

        return $user;
    }

    /**
     * Подтверждение регистрации пользователя.
     */
    public function confirmSignUp(): void
    {
        if (!$this->getStatus()->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }

        $this->status = Status::active();
        $this->confirmToken = null;
    }

    /**
     * Регистрация пользователя по социальной сети.
     *
     * @param Id $id
     * @param \DateTimeImmutable $date
     * @param string $network
     * @param string $identity
     * @return static
     */
    public static function signUpByNetwork(Id $id, \DateTimeImmutable $date, string $network, string $identity): self
    {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = Status::active();

        return $user;
    }

    /**
     * Привязка социальной сети к аккаунту.
     *
     * @param string $network
     * @param string $identity
     */
    public function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isForNetwork($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * Отправка запроса на смену электронной почты.
     *
     * @param Email $email
     * @param string $token
     */
    public function requestEmailChanging(Email $email, string $token): void
    {
        if (!$this->getStatus()->isActive()) {
            throw new UserNotActiveException('Аккаунт не активен.');
        }

        if ($this->email && $this->email->isEqual($email)) {
            throw new UserAlreadySameEmail('Электронная почта уже привязана к акканту.');
        }

        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    /**
     * Подтверждение смены электронной почты.
     *
     * @param string $token
     */
    public function confirmEmailChanging(string $token): void
    {
        if (!$this->getNewEmail()) {
            throw new UserEmailChangingNotRequested('Смена электронной почты не была запрошена.');
        }

        if ($this->getNewEmailToken() !== $token) {
            throw new UserInvalidNewEmailToken('Неверный токен смены электронной почты.');
        }

        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    /**
     * Отправка запроса на восстановление пароля.
     *
     * @param ResetToken $token
     * @param \DateTimeImmutable $date
     */
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

    /**
     * Восстановление пароля.
     *
     * @param \DateTimeImmutable $date
     * @param string $hash
     */
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

    /**
     * Изменение роли.
     *
     * @param Role $role
     */
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

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
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

    /**
     * @ORM\PostLoad
     */
    public function checkEmbeds(): void
    {
        if ($this->resetToken->isEmpty()) {
            $this->resetToken = null;
        }
    }
}
