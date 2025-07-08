<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    //    /**
    //     * @return Etudiant[] Returns an array of Etudiant objects
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
     * Compte les nouveaux étudiants inscrits dans les X derniers jours
     */
    public function countNouveauxEtudiants(int $jours): int
    {
        $date = new \DateTime();
        $date->modify("-{$jours} days");
        
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.id >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Obtient l'évolution mensuelle des inscriptions
     */
    public function getEvolutionMensuelle(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('MONTH(e.id) as mois, COUNT(e.id) as total')
            ->where('e.id >= :date')
            ->setParameter('date', new \DateTime('-12 months'))
            ->groupBy('mois')
            ->orderBy('mois', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
