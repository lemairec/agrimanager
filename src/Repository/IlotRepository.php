<?php

namespace App\Repository;
use App\Entity\Ilot;

/**
 * IlotRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IlotRepository extends \Doctrine\ORM\EntityRepository
{
    function add($name, $surface){
        $em = $this->getEntityManager();
        $ilot = new Ilot();
        $ilot->surface = $surface;
        $ilot->name = $name;
        $em->persist($ilot);
        $em->flush();
        return $ilot;
    }

    function getById($ilot_id){
        return $this->findOneBy(array('id' => $ilot_id));
    }

    function getAllforCompany($company){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBY('p.number', 'ASC')
            ->addorderBy('p.surface', 'ASC')
            ->setParameter('company', $company)
            ->getQuery();

        return $query->getResult();
    }
}
