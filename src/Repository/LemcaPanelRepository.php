<?php

namespace App\Repository;

use App\Entity\LemcaPanel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LemcaPanel|null find($id, $lockMode = null, $lockVersion = null)
 * @method LemcaPanel|null findOneBy(array $criteria, array $orderBy = null)
 * @method LemcaPanel[]    findAll()
 * @method LemcaPanel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LemcaPanelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LemcaPanel::class);
    }

    // /**
    //  * @return LemcaPanel[] Returns an array of LemcaPanel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LemcaPanel
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
