<?php

namespace App\Repository\Silo;

use App\Entity\Silo\Temperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiloTemperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiloTemperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiloTemperature[]    findAll()
 * @method SiloTemperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperature::class);
    }

    function getAllForBalise($balise){
        return $this->createQueryBuilder('p')
            ->where('p.balise = :balise')
            ->orderBy('p.datetime')
            ->setParameter('balise', $balise)
            ->getQuery()->getResult();
    }

    // /**
    //  * @return SiloTemperature[] Returns an array of SiloTemperature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiloTemperature
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
