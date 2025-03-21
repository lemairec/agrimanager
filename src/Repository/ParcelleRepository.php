<?php

namespace App\Repository;
use App\Entity\Parcelle;
use App\Entity\Intervention;

/**
 * ParcelleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParcelleRepository extends \Doctrine\ORM\EntityRepository
{
    function save($parcelle){
        $em = $this->getEntityManager();
        $parcelle->completeName = $parcelle->getCultureName();
        if($parcelle->ilot){
            $parcelle->completeName = $parcelle->completeName." - ".$parcelle->getIlotName();
        }
        if($parcelle->name){
            $parcelle->completeName = $parcelle->completeName." - ".$parcelle->name;
        }


        $em->persist($parcelle);
        $em->flush();
        $interventions =  $em->getRepository(Intervention::class)->getAllForParcelle($parcelle);
        foreach($interventions as $intervention){
            $em->getRepository(Intervention::class)->updateSurface($intervention->id);
        }
        return $parcelle;

    }

    function getAllForCampagne($campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->addorderBy('p.active', 'DESC')
            ->addorderBy('p.completeName', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    function getAllForIlot($ilot){
        $query = $this->createQueryBuilder('p')
            ->join('p.campagne','c')
            ->where('p.ilot = :ilot')
            ->setParameter('ilot', $ilot)
            ->addorderBy('c.name', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    function getAllForIlotCampagne($ilot, $campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.ilot = :ilot')
            ->andWhere('p.campagne = :campagne')
            ->setParameter('ilot', $ilot)
            ->setParameter('campagne', $campagne)
            ->addorderBy('p.name', 'DESC')
            ->addorderBy('p.surface', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    function getSumForCultureCampagne($culture, $campagne){
        $res = $this->createQueryBuilder('p')
            ->where('p.culture = :culture')
            ->andWhere('p.campagne = :campagne')
            ->setParameter('culture', $culture)
            ->setParameter('campagne', $campagne)
            ->select('SUM(p.surface) as sum')
            ->getQuery()

            ->getOneOrNullResult();

            return $res['sum'];
    }

    function getAllForCampagneWithoutActive($campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->andWhere('p.active = true')
            ->setParameter('campagne', $campagne)
            ->addorderBy('p.active', 'DESC')
            ->addorderBy('p.completeName', 'ASC')
            ->getQuery();

        return $query->getResult();
    }


    function delete($parcelle_id){
        $em = $this->getEntityManager();
        $parcelle = $this->findOneById($parcelle_id);
        $em->remove($parcelle);
        $em->flush();
    }

    public function countByCompany(){

        $em = $this->getEntityManager();
        $statement = $em->getConnection()->prepare('SELECT c.company_id as company_id, count(*) as count FROM `parcelle` p inner join campagne c  on c.id=p.campagne_id group by c.company_id');
        $result = $statement->executeQuery();

        return $ruleResult = $result->fetchAssociative();
    }
}
