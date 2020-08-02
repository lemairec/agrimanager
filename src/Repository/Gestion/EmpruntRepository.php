<?php

namespace App\Repository\Gestion;

use App\Entity\Gestion\Emprunt;
use App\Entity\Gestion\Ecriture;
use App\Entity\Gestion\Operation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    function getAllForCompany($company){
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->orderBy('p.date', 'DESC')
            ->setParameter('company', $company)
            ->getQuery()->getResult();
    }

    function save($emprunt){
        $em = $this->getEntityManager();

        $em->persist($emprunt);
        $em->flush();

        $operation_name = "emprunt ".$emprunt->id." ".$emprunt->name;
        $operation = $em->getRepository('App:Gestion\Operation')->findOneByName($operation_name);
        if($operation == NULL){
            $operation = new Operation();
            $operation->company = $emprunt->company;
            $operation->name = $operation_name;
            $operation->date = $emprunt->date;
            $operation->emprunt = $emprunt;
        }
        

        $em->persist($operation);
        $em->flush();

        foreach($operation->ecritures as $e){
            $em->remove($e);
        }
        $em->flush();

        $ecriture = new Ecriture();
        $ecriture->compte = $emprunt->compteEmprunt;
        $ecriture->operation = $operation;
        $ecriture->value = $emprunt->montant;
        $em->persist($ecriture);

        $ecriture = new Ecriture();
        $ecriture->compte = $emprunt->compte;
        $ecriture->operation = $operation;
        $ecriture->value = -$emprunt->montant;
        $em->persist($ecriture);

        $em->flush();

    }

    // /**
    //  * @return Emprunt[] Returns an array of Emprunt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Emprunt
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
