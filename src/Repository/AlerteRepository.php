<?php

namespace App\Repository;

use App\Entity\Alerte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Alerte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alerte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alerte[]    findAll()
 * @method Alerte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlerteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Alerte::class);
    }

    public function removeAlerteCampagne($campagne){
        $alertes = $this->findByCampagne($campagne);
        $em = $this->getEntityManager();
        foreach ($alertes as $alerte) {
            $em->remove($alerte);
        }
        $em->flush();
    }

    public function verifyCampagne($campagne){
        $em = $this->getEntityManager();
        $its = $em->getRepository('App:Intervention')->findByCampagne($campagne);
        foreach ($its as $it) {
            $em->getRepository('App:Alerte')->verifyMelange($it);
            $em->getRepository('App:Alerte')->verifyIntervention($it);
        }
    }


    public function createAlerte($intervention, $type,  $message, $interventionProduit, $interventionParcelle){
        $em = $this->getEntityManager();

        $alerte = new Alerte();
        $alerte->campagne = $intervention->campagne;
        $alerte->interventionProduit = $interventionProduit;
        $alerte->intervention = $intervention;
        $alerte->type = $type;
        $alerte->interventionParcelle = $interventionParcelle;
        $alerte->description = $message;

        $em->persist($alerte);
        $em->flush();
    }

    public function verifyMelange($intervention){
        $em = $this->getEntityManager();

        $produitT = null;
        foreach ($intervention->produits as $p) {

            if($p->produit->ephyProduit){
                if($p->produit->ephyProduit->isT()){
                    if($produitT){
                        $this->createAlerte($intervention, "Melange2", "Melange2 entre ".$produitT->produit." et ".$p->produit, $p, null);
                    }
                    $produitT = $p;
                }
            }
        }
    }

    public function verifyIntervention($intervention){
        $em = $this->getEntityManager();

        //if($intervention->id != "93c08d50-395b-11e7-92c4-80e65014bb7c"){
        //    return;
        //}
        foreach ($intervention->produits as $p) {
            if($p->produit->ephyProduit){
                $usages = $em->getRepository('App:EphyUsage')->findByEphyProduit($p->produit->ephyProduit);
                $qty_ha = $p->getQtyHa();
                foreach ($intervention->parcelles as $parcelle) {
                    if($parcelle->parcelle->culture->metaCulture){
                        $cultures2 =  explode(",", $parcelle->parcelle->culture->metaCulture->cultureUsage);
                        $cultures = [];
                        foreach($cultures2 as $c){
                            if($c != ""){
                                $cultures[] = $c;
                            }
                        }
                        //print(json_encode($cultures));
                        $find = false;
                        foreach($usages as $u){
                            foreach($cultures as $c){
                                if(!$find){
                                    $pos = strpos($u->identifiantUsage, $c);
                                    if($pos !== false){
                                        $find=true;
                                        if($u->doseRetenu < $qty_ha){
                                            $res =["utilise" => $qty_ha, "max" => $u->doseRetenu];
                                            $this->createAlerte($intervention, "Quantite", "{ utilise :".number_format($qty_ha, 3, ',', ' ').", max ".number_format($u->doseRetenu, 3, ',', ' '), $p, $parcelle);
                                        }
                                    }
                                }
                            }
                        }
                        if(!$find){
                            $this->createAlerte($intervention, "Usage not found", "", $p, $parcelle);
                        }
                    } else {
                        $this->createAlerte($intervention, "Usage not found", "", $p, $parcelle);
                    }
                }


            }
        }
    }

//    /**
//     * @return Alerte[] Returns an array of Alerte objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Alerte
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}