<?php

namespace App\Repository\Ruche;

use App\Entity\Ruche\Ruche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ruche|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ruche|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ruche[]    findAll()
 * @method Ruche[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RucheRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ruche::class);
    }

//    /**
//     * @return Ruche[] Returns an array of Ruche objects
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
    public function findOneBySomeField($value): ?Ruche
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
