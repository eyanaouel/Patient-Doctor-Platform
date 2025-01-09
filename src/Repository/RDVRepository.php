<?php

namespace App\Repository;

use App\Entity\RDV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RDV>
 */
class RDVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RDV::class);
    }

    // src/Repository/RDVRepository.php

public function findByDateAndStatus(Patient $patient, Medecin $medecin, \DateTime $date)
{
    $qb = $this->createQueryBuilder('r')
        ->where('r.patient = :patient')
        ->andWhere('r.medecin = :medecin')
        ->andWhere('r.statut = :statut')
        ->andWhere('DATE(r.dateHeure) = :date')
        ->setParameter('patient', $patient)
        ->setParameter('medecin', $medecin)
        ->setParameter('statut', 'en_attente')
        ->setParameter('date', $date->format('Y-m-d'));

    return $qb->getQuery()->getOneOrNullResult();
}

    //    /**
    //     * @return RDV[] Returns an array of RDV objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RDV
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
