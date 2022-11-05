<?php

namespace App\Repository\Ruche;

use App\Entity\Ruche\Essaim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Essaim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Essaim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Essaim[]    findAll()
 * @method Essaim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EssaimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Essaim::class);
    }

    public function findAll() : array 
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.actif', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Essaim[] Returns an array of Essaim objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Essaim
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
