<?php

namespace App\Repository\Commercialisation;

use App\Entity\Commercialisation\toto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method toto|null find($id, $lockMode = null, $lockVersion = null)
 * @method toto|null findOneBy(array $criteria, array $orderBy = null)
 * @method toto[]    findAll()
 * @method toto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class totoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, toto::class);
    }

//    /**
//     * @return toto[] Returns an array of toto objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?toto
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
