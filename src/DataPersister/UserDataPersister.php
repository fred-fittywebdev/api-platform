<?php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;

class UserDataPersister implements DataPersisterInterface
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}