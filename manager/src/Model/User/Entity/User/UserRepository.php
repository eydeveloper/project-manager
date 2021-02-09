<?php

namespace App\Model\User\Entity\User;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function findByConfirmToken(string $token): ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['confirmToken' => $token]);

        return $user;
    }

    public function findByResetToken(string $token): ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['resetToken.token' => $token]);

        return $user;
    }

    public function get(Id $id): User
    {
        /** @var $user User */
        if (!$user = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        /** @var $user User */
        if (!$user = $this->repository->findBy(['email' => $email->getValue()])) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repository->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->innerJoin('u.networks', 'n')
            ->andWhere('n.network = :network and n.identity = :identity')
            ->setParameter(':networks', $network)
            ->setParameter(':identity', $identity)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }
}
