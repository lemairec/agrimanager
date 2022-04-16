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

    public function getAllByCompany($company)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.company = :company')
            ->setParameter('company', $company)
            ->orderBy('b.datetime', 'ASC')
            ->orderBy('b.enable', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

    public function getOneByMyId($my_id): ?Balise
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.myId = :my_id')
            ->setParameter('my_id', $my_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
