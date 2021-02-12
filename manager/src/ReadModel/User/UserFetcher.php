<?php

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserFetcher
{
    private Connection $connection;

    /**
     * UserFetcher constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $token
     * @return mixed
     * @throws Exception
     */
    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter(':token', $token)
            ->execute()->fetchOne();
    }

    /**
     * @param string $email
     * @return AuthView|null
     * @throws Exception
     */
    public function findForAuth(string $email): ?AuthView
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute()
            ->fetchAllAssociative();

        if (!$user = array_shift($result)) {
            return null;
        }

        return AuthView::fromArray($user);
    }
}
