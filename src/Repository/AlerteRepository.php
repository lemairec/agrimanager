<?php

namespace App\Repository;

use App\Entity\Alerte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alerte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alerte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alerte[]    findAll()
 * @method Alerte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlerteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
        $nbephy = 0;
        $nbcat1 = 0;
        $nbcat2 = 0;
        $nbcat3 = 0;
        $nbcat4 = 0;
        foreach ($intervention->produits as $p) {

            if($p->produit->ephyProduit){
                $nbephy = $nbephy+1;
                if($p->produit->ephyProduit->isCategory1()){
                    $nbcat1 = $nbcat1+1;
                } else if($p->produit->ephyProduit->isCategory2()){
                    $nbcat2 = $nbcat2+1;
                } else if($p->produit->ephyProduit->isCategory3()){
                    $nbcat3 = $nbcat3+1;
                } else if($p->produit->ephyProduit->isCategory4()){
                    $nbcat4 = $nbcat4+1;
                }
            }
        }

        //$this->createAlerte($intervention, "Melange2", "Melange $nbephy $nbcat1 $nbcat2 $nbcat3 $nbcat4", null, null);
        if($nbcat1 > 0 && $nbephy > 1){
            $this->createAlerte($intervention, "Melange2", "Melange interdit", null, null);
        }

        if($nbcat2 > 1){
            $this->createAlerte($intervention, "Melange2", "Melange interdit", null, null);
        }

        if($nbcat3 > 1){
            $this->createAlerte($intervention, "Melange2", "Melange interdit", null, null);
        }

        if($nbcat4 > 1){
            $this->createAlerte($intervention, "Melange2", "Melange interdit", null, null);
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
                $qty_ha = $p->getQuantityHa();
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
