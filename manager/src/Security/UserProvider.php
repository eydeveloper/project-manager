<?php

namespace App\Security;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserFetcher $users;

    /**
     * UserProvider constructor.
     *
     * @param UserFetcher $users
     */
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @param string $username
     * @return UserIdentity
     */
    public function loadUserByUsername(string $username): UserIdentity
    {
        $user = $this->users->findForAuth($username);

        if (!$user) {
            throw new UsernameNotFoundException('');
        }

        return new UserIdentity($user->id, $user->username, $user->password_hash, $user->role);
    }

    /**
     * @param UserInterface $user
     * @return UserInterface|UserIdentity
     */
    public function refreshUser(UserInterface $user): UserInterface|UserIdentity
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . get_class($user));
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return $class instanceof UserIdentity;
    }
}
