<?php

namespace App\Repository;

use App\Entity\GpsParcelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GpsParcelle|null find($id, $lockMode = null, $lockVersion = null)
 * @method GpsParcelle|null findOneBy(array $criteria, array $orderBy = null)
 * @method GpsParcelle[]    findAll()
 * @method GpsParcelle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GpsParcelleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GpsParcelle::class);
    }

    // /**
    //  * @return GpsParcelle[] Returns an array of GpsParcelle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function getActiveByNameCompany($name, $company)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.name = :name')
            ->andWhere('g.active = true')
            ->andWhere('g.company = :company')
            ->setParameter('company', $company)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllByNameCompany($name, $company)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.name = :name')
            ->setParameter('name', $name)
            ->andWhere('g.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllByCompany($company)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.active = true')
            ->andWhere('g.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult()
        ;
    }
}
