<?php

namespace App\Repository\Iot;

use App\Entity\Iot\Sechoir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sechoir>
 */
class SechoirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sechoir::class);
    }

    public function getAll($duree): array
        {
            $i = intval($duree);
            if($i == null){
                $i = 1000;
            }
            return $this->createQueryBuilder('s')
                ->orderBy('s.id', 'DESC')
                ->setMaxResults($i)
                ->getQuery()
                ->getResult()
            ;
        }

     public function getAllBE($begin, $end): array
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.datetime > :begin')
                ->andWhere('s.datetime < :end')
                ->setParameter('begin', $begin)
                ->setParameter('end', $end)
                ->orderBy('s.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    /**
    //     * @return Sechoir[] Returns an array of Sechoir objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sechoir
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
