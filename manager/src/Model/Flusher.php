<?php

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
