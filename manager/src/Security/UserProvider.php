<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Driver\Exception as DoctrineDriverException;
use Doctrine\DBAL\Exception as DoctrineException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     * @throws DoctrineException
     */
    public function loadUserByUsername(string $username): UserIdentity
    {
        $user = $this->loadUser($username);

        return self::identityByUser($user, $username);
    }

    /**
     * {@inheritdoc}
     * @throws DoctrineException
     */
    public function refreshUser(UserInterface $user): UserInterface|UserIdentity
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . get_class($user));
        }

        return self::identityByUser(
            $this->loadUser($user->getUsername()),
            $user->getUsername()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class;
    }

    /**
     * @param string $username
     * @return AuthView
     * @throws DoctrineException
     * @throws DoctrineDriverException
     */
    public function loadUser(string $username): AuthView
    {
        $chunks = explode(':', $username);

        if (count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        return throw new UsernameNotFoundException('');
    }

    #[Pure]
    public static function identityByUser(AuthView $user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email ?: $username,
            $user->password_hash ?: '',
            $user->status,
            $user->role
        );
    }
}
