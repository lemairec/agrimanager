<?php

namespace App\Repository\Agrigps;

use App\Entity\Agrigps\Balise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Balise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Balise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Balise[]    findAll()
 * @method Balise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaliseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Balise::class);
    }

    // /**
    //  * @return Balise[] Returns an array of Balise objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Balise
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
