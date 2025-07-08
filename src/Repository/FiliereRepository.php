<?php

namespace App\Repository;

use App\Entity\Filiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Filiere>
 */
class FiliereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filiere::class);
    }

    /**
     * Trouve les filières avec le plus d'étudiants
     */
    public function findTopFilieres(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->select('f.nom, COUNT(ia.id) as totalEtudiants')
            ->leftJoin('App\Entity\InformationAcademique', 'ia', 'WITH', 'ia.filiere = f.id')
            ->groupBy('f.id')
            ->orderBy('totalEtudiants', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
