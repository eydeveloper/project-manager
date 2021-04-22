<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectRepository;

class UserRepository
{
    private EntityManagerInterface $entityManager;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * Поиск пользователя по токену подтверждения регистрации.
     *
     * @param string $token Токен подтверждения регистрации.
     * @return User|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmToken' => $token]);
    }

    /**
     * Поиск пользователя по токену восстановления пароля.
     *
     * @param string $token Токен восстановления пароля.
     * @return User|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repository->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * Получение пользователя по идентификатору.
     *
     * @param Id $id Идентификатор.
     * @return User
     */
    public function get(Id $id): User
    {
        /** @var $user User */
        if (!$user = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * Получение пользователя по электронной почте.
     *
     * @param Email $email Электронная почта.
     * @return User
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repository->findOneBy(['email' => $email->getValue()])) {
            throw new EntityNotFoundException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * Проверка существования пользователя по электронной почте.
     *
     * @param Email $email Электронная почта.
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->andWhere('u.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * Проверка существования пользователя по социальной сети.
     *
     * @param string $network Название социальной сети.
     * @param string $identity Идентификатор пользователя в социальной сети.
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repository->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->innerJoin('u.networks', 'n')
                ->andWhere('n.network = :network AND n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * Добавление пользователя.
     *
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }
}
