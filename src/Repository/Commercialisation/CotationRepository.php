<?php

namespace App\Repository\Commercialisation;

use App\Entity\Commercialisation\Cotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cotation[]    findAll()
 * @method Cotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotation::class);
    }

    public function add($cotation){
        $em = $this->getEntityManager();
        $cotation2 = $this->get($cotation->source, $cotation->campagne, $cotation->produit, $cotation->date->format("Y-m-d"));
        if($cotation2){
            $em->remove($cotation2);
            $em->flush();
        }
        $em->persist($cotation);
        $em->flush();
    }

    public function get($source, $campagne, $produit, $date){
        return $this->createQueryBuilder('p')
            ->where('p.source = :source')
            ->andWhere('p.campagne = :campagne')
            ->andWhere('p.produit = :produit')
            ->andWhere('p.date = :date')
            ->setParameter('source', $source)
            ->setParameter('campagne', $campagne)
            ->setParameter('produit', $produit)
            ->setParameter('date', $date)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function getLast($source, $campagne, $produit){
        return $this->createQueryBuilder('p')
            ->where('p.source = :source')
            ->andWhere('p.campagne = :campagne')
            ->andWhere('p.produit = :produit')
            ->orderBy('p.date', 'DESC')
            ->setParameter('source', $source)
            ->setParameter('campagne', $campagne)
            ->setParameter('produit', $produit)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function getAll($source, $campagne, $produit){
        return $this->createQueryBuilder('p')
            ->where('p.source = :source')
            ->andWhere('p.campagne = :campagne')
            ->andWhere('p.produit = :produit')
            ->orderBy('p.date', 'DESC')
            ->setParameter('source', $source)
            ->setParameter('campagne', $campagne)
            ->setParameter('produit', $produit)
            ->getQuery()->getResult();
    }

    public function getLasts(){

        $em = $this->getEntityManager();
        $statement = $em->getConnection()->prepare('SELECT c.date as mydate FROM cotation c order by c.date desc');
        $statement->execute();

        $date =  $statement->fetchAll()[0]["mydate"];

        $res = $this->createQueryBuilder('p')
            ->where('p.date = :date')
            ->addOrderBy('p.campagne', 'ASC')
            ->addOrderBy('p.produit', 'ASC')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        return $res;


    }

//    /**
//     * @return Cotation[] Returns an array of Cotation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cotation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
