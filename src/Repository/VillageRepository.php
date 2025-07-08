<?php

namespace App\Repository;

use App\Entity\Village;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Village>
 */
class VillageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Village::class);
    }

    /**
     * Trouve les villages avec le plus d'étudiants
     */
    public function findTopVillages(int $limit = 5): array
    {
        return $this->createQueryBuilder('v')
            ->select('v.nom, COUNT(e.id) as totalEtudiants')
            ->leftJoin('App\Entity\Etudiant', 'e', 'WITH', 'e.village = v.id')
            ->groupBy('v.id')
            ->orderBy('totalEtudiants', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
