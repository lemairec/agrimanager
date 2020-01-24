<?php

namespace App\Repository;

use App\Entity\MetaCulture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetaCulture|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetaCulture|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetaCulture[]    findAll()
 * @method MetaCulture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetaCultureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetaCulture::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?MetaCulture
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
