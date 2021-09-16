<?php

namespace App\Repository;

use App\Entity\GpsParcelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GpsParcelle|null find($id, $lockMode = null, $lockVersion = null)
 * @method GpsParcelle|null findOneBy(array $criteria, array $orderBy = null)
 * @method GpsParcelle[]    findAll()
 * @method GpsParcelle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GpsParcelleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GpsParcelle::class);
    }

    // /**
    //  * @return GpsParcelle[] Returns an array of GpsParcelle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GpsParcelle
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
