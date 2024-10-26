<?php

namespace App\Repository;

/**
 * EphyCommercialNameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EphyCommercialNameRepository extends \Doctrine\ORM\EntityRepository
{
    public function save($ephyCommercialName){
        $ephyCommercialNameBdd = $this->find($ephyCommercialName->name);
        if($ephyCommercialNameBdd != null){
            //print("\n"."commercialName already ".$ephyCommercialName->name);
        } else {
            $em = $this->getEntityManager();
            $em->persist($ephyCommercialName);
            $em->flush();
        }

    }
}
