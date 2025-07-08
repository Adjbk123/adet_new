<?php

namespace App\Repository;

use App\Entity\Etablissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etablissement>
 */
class EtablissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etablissement::class);
    }

    //    /**
    //     * @return Etablissement[] Returns an array of Etablissement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    /**
     * Trouve les établissements avec le plus d'étudiants
     */
    public function findTopEtablissements(int $limit = 5): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.nom, COUNT(ia.id) as totalEtudiants')
            ->leftJoin('App\Entity\InformationAcademique', 'ia', 'WITH', 'ia.etablissement = e.id')
            ->groupBy('e.id')
            ->orderBy('totalEtudiants', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
