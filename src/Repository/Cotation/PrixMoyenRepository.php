<?php

namespace App\Repository\Cotation;

use App\Entity\Cotation\PrixMoyen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrixMoyen>
 */
class PrixMoyenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrixMoyen::class);
    }

    public function getAlls(){
        return $this->createQueryBuilder('p')
            ->orderBy('p.campagne', 'DESC')
            ->getQuery()->getResult();
    }

    public function getAllProduitAndCampagne($produit, $campagne){
        return $this->createQueryBuilder('p')
            ->andWhere('p.produit = :produit')
            ->andWhere('p.campagne = :campagne')
            ->setParameter('produit', $produit)
            ->setParameter('campagne', $campagne)
            ->getQuery()->getResult();
    }

    //    /**
    //     * @return PrixMoyen[] Returns an array of PrixMoyen objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PrixMoyen
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
