<?php

namespace App\Repository\Silo;

use App\Entity\Silo\Balise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiloBalise|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiloBalise|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiloBalise[]    findAll()
 * @method SiloBalise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaliseRepository extends \Doctrine\ORM\EntityRepository
{
    public function getOrCreate($company, $name){
        $balise = $this->createQueryBuilder('s')
            ->andWhere('s.company = :company')
            ->setParameter('company', $company)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;


        if($balise == NULL){
            $balise = new Balise();
            $balise->company = $company;
            $balise->name = $name;

            $em = $this->getEntityManager();
            $em->persist($balise);
            $em->flush();
        }

        return $balise;
    }

    function getAllForCompany($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBy('p.label')
            ->setParameter('company', $company)
            ->getQuery()->getResult();
    }

    // /**
    //  * @return SiloBalise[] Returns an array of SiloBalise objects
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
    public function findOneBySomeField($value): ?SiloBalise
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
