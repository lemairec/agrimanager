<?php

namespace App\Repository\Gestion;

/**
 * CompteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompteRepository extends \Doctrine\ORM\EntityRepository
{
    function getAllForCompany($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBy('p.name')
            ->setParameter('company', $company)
            ->getQuery()->getResult();
    }

    function getAllBanques($company){
        return $this->createQueryBuilder('p')
            ->where("p.company = :company")
            ->andWhere("p.type = 'banque'")
            ->setParameter('company', $company)
            ->orderBy('p.name')
            ->getQuery()->getResult();
    }

    function getFirstBanque($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->andWhere("p.type = 'banque'")
            ->setParameter('company', $company)
            ->orderBy('p.name')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    function getNoBanques($company){
        return $this->createQueryBuilder('p')
            ->where("p.type != 'banque'")
            ->andWhere('p.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.name')
            ->getQuery()->getResult();
    }

    function getCompteTVA($company){
        return $this->createQueryBuilder('p')
            ->where("p.type = 'tva'")
            ->andWhere('p.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.name')
            ->getQuery()->getResult()[0];
    }
}