<?php

namespace App\Repository;

use App\Entity\JobGps;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method JobGps|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobGps|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobGps[]    findAll()
 * @method JobGps[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobGpsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobGps::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.dateBegin', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.user = :user')
            ->setParameter('user', $user)
            ->orderBy('j.dateBegin', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?JobGps
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
