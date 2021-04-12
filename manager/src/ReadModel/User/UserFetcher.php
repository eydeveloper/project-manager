<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Метод проверяет наличие пользователя по токену восстановления.
     *
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function existsByResetToken(string $token): bool
    {
        $user = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter(':token', $token)
            ->execute()
            ->fetchOne();

        return (bool)$user;
    }

    /**
     * Метод выполняет поиск пользователя по e-mail для авторизации.
     *
     * @param string $email
     * @return AuthView|null
     * @throws Exception
     */
    public function findForAuthByEmail(string $email): ?AuthView
    {
        $user = $this->connection->createQueryBuilder()
            ->select([
                'id',
                'email',
                'password_hash',
                'role',
                'status',
            ])
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute()
            ->fetchAssociative();

        if (!$user) {
            return null;
        }

        return AuthView::fromArray($user);
    }

    /**
     * Метод выполняет поиск пользователя по социальной сети для авторизации.
     *
     * @param string $network
     * @param string $identity
     * @return AuthView|null
     * @throws Exception
     */
    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $result = $this->connection->createQueryBuilder()
            ->select([
                'u.id',
                'u.email',
                'u.password_hash',
                'u.role',
                'u.status',
            ])
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter('network', $network)
            ->setParameter('identity', $identity)
            ->execute()
            ->fetchAllAssociative();

        if (!$user = array_shift($result)) {
            return null;
        }

        return AuthView::fromArray($user);
    }

    /**
     * Метод выполняет поиск пользователя по e-mail.
     *
     * @param string $email
     * @return ShortView|null
     * @throws Exception
     */
    public function findByEmail(string $email): ?ShortView
    {
        $user = $this->connection->createQueryBuilder()
            ->select([
                'id',
                'email',
                'role',
                'status',
            ])
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute()
            ->fetchAssociative();

        if (!$user) {
            return null;
        }

        return ShortView::fromArray($user);
    }
}
