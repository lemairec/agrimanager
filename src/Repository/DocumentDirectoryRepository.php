<?php

namespace App\Repository;

use App\Entity\DocumentDirectory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentDirectory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentDirectory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentDirectory[]    findAll()
 * @method DocumentDirectory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentDirectoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentDirectory::class);
    }

    public function findAll(){
        return $this->createQueryBuilder('d')
            ->orderBy('d.ordre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return DocumentDirectory[] Returns an array of DocumentDirectory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentDirectory
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
