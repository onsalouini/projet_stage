<?php
// src/Repository/PresenceRepository.php
namespace App\Repository;

use App\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    public function findPresenceByDate(\DateTime $date): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.statut, COUNT(p.id) as count')
            ->where('p.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->groupBy('p.statut')
            ->getQuery()
            ->getResult();
    }
}
