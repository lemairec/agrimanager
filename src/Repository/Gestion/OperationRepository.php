<?php

namespace App\Repository\Gestion;

/**
 * OperationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationRepository extends \Doctrine\ORM\EntityRepository
{
    function getAllForCompte($compte){
        $em = $this->getEntityManager();
        $sql = 'SELECT operation_id FROM ecriture where compte_id = ?';

        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->bindValue(1, $compte->id);
        $statement->execute();
        $res = $statement->fetchAll();
        $ids = [];
        foreach($res as $p){
            $ids[] = $p["operation_id"];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('p.date', 'ASC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllForBanque($company){
        
        $em = $this->getEntityManager();
        $sql = 'SELECT operation_id FROM ecriture e join compte c on e.compte_id=c.id where c.type = "banque" and c.company_id = "'.$company->id.'"';

        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        $res = $statement->fetchAll();
        $ids = [];
        foreach($res as $p){
            $ids[] = $p["operation_id"];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('p.date', 'ASC')
            ->getQuery();

            return $query->getResult();
    }

    function getAllAsc(){
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.date', 'ASC')
            ->getQuery();

            return $query->getResult();
    }


    function getAllForCompany($company){
        $query = $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    function deleteForFacture($facture){
        $em = $this->getEntityManager();
        $operations = $this->findByFacture($facture);
        foreach($operations as $o){
            foreach($o->ecritures as $e){
                $em->remove($e);
            }
            $em->remove($o);
        }
        $em->flush();
    }

    function getForFacture($facture){
        return $this->findByFacture($facture);
    }

    function delete($operation_id){
        $em = $this->getEntityManager();
        $operation = $this->findOneById($operation_id);

        foreach($operation->ecritures as $e){
            $em->remove($e);

        }
        if($operation->facture){
            $em->remove($operation->facture);
        }
        $em->remove($operation);
        $em->flush();


    }
}