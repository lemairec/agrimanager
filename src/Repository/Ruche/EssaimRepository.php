<?php

namespace App\Repository\Ruche;

use App\Entity\Ruche\Essaim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Essaim|null find($id, $lockMode = null, $lockVersion = null)
 * @method Essaim|null findOneBy(array $criteria, array $orderBy = null)
 * @method Essaim[]    findAll()
 * @method Essaim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EssaimRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Essaim::class);
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
