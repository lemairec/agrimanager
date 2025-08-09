<?php

namespace App\Repository\Iot;

use App\Entity\Iot\Moteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiloBalise|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiloBalise|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiloBalise[]    findAll()
 * @method SiloBalise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoteurRepository extends \Doctrine\ORM\EntityRepository
{
    public function getOrCreate($company, $name){
        $moteur = $this->createQueryBuilder('s')
            ->andWhere('s.company = :company')
            ->setParameter('company', $company)
            ->andWhere('s.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;


        if($moteur == NULL){
            $moteur = new Moteur();
            $moteur->company = $company;
            $moteur->name = $name;

            $em = $this->getEntityManager();
            $em->persist($moteur);
            $em->flush();
        }

        return $moteur;
    }

    function getAllForCompany($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBy('p.label')
            ->setParameter('company', $company)
            ->getQuery()->getResult();
    }
}
