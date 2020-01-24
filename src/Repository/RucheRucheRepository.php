<?php

namespace App\Repository;

use App\Entity\RucheRuche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RucheRuche|null find($id, $lockMode = null, $lockVersion = null)
 * @method RucheRuche|null findOneBy(array $criteria, array $orderBy = null)
 * @method RucheRuche[]    findAll()
 * @method RucheRuche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RucheRucheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RucheRuche::class);
    }

//    /**
//     * @return RucheRuche[] Returns an array of RucheRuche objects
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
    public function findOneBySomeField($value): ?RucheRuche
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
