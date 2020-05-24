<?php

namespace App\Repository\Ruche;

use App\Entity\Ruche\Rucher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rucher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rucher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rucher[]    findAll()
 * @method Rucher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RucherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rucher::class);
    }

    public function getAll()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.lieu', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Rucher[] Returns an array of Rucher objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rucher
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
