<?php

namespace App\Repository\Gestion;

use App\Entity\Gestion\Ecriture;
use App\Entity\Gestion\Operation;
use App\Entity\Gestion\Compte;

/**
 * FactureFournisseurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactureFournisseurRepository extends \Doctrine\ORM\EntityRepository
{
    function save($facture){
        $em = $this->getEntityManager();

        $em->getRepository(Operation::class)->deleteForFacture($facture);

        $em->persist($facture);
        $em->flush();

        $operation = new Operation();
        $operation->company = $facture->company;
        $operation->name = $facture->getCompleteName();
        $operation->date = $facture->paiementDate;
        $operation->facture = $facture;

        $em->persist($operation);
        $em->flush();

        $ecriture = new Ecriture();
        $ecriture->compte = $facture->banque;
        $ecriture->operation = $operation;
        $ecriture->value = $facture->montantTTC;
        $em->persist($ecriture);

        $ecriture = new Ecriture();
        $ecriture->compte = $facture->compte;
        $ecriture->operation = $operation;
        $ecriture->value = -$facture->montantHT;
        $ecriture->campagne = $facture->campagne;
        $em->persist($ecriture);

        if($facture->montantTTC!=$facture->montantHT){
            $ecriture = new Ecriture();
            $ecriture->compte = $em->getRepository(Compte::class)->getCompteTVA($facture->company);
            $ecriture->operation = $operation;
            $ecriture->value = -($facture->montantTTC-$facture->montantHT);
            $em->persist($ecriture);
        }
        $em->flush();

    }

    function delete($facture){
        $em = $this->getEntityManager();

        $em->getRepository(Operation::class)->deleteForFacture($facture);
        $em->remove($facture);
        $em->flush();
    }

    function getAllForCompany($company){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllForExport($company){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->andWhere('p.dateExport is NULL')
            ->setParameter('company', $company)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllForExport2($company, $date_begin, $date_end){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->andWhere('p.paiementDate > :date_begin')
            ->andWhere('p.paiementDate < :date_end')
            ->setParameter('company', $company)
            ->setParameter('date_end', $date_end)
            ->setParameter('date_begin', $date_begin)
            ->orderBy('p.paiementDate', 'DESC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllForCampagne($campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->orWhere('p.campagne is NULL')
            ->setParameter('campagne', $campagne)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllForCompteCampagne($compte, $campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.campagne = :campagne')
            ->andWhere('p.compte = :compte')
            ->setParameter('campagne', $campagne)
            ->setParameter('compte', $compte)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

            return $query->getResult();
    }
}
