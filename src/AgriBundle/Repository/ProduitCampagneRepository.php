<?php

namespace AgriBundle\Repository;
use AgriBundle\Entity\ProduitCampagne;

/**
 * ProduitCampagneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProduitCampagneRepository extends \Doctrine\ORM\EntityRepository
{
    public function update($produit){
        $em = $this->getEntityManager();

        $ps = $this->findByProduit($produit);
        foreach($ps as $p){
            $em->remove($p);
        }
        $em->flush();

        $campagnes = $em->getRepository('AgriBundle:Campagne')->findByCompany($produit->company);
        foreach($campagnes as $campagne){
            $achats = $em->getRepository('AgriBundle:Achat')->createQueryBuilder('a')
                ->where('a.campagne = :campagne')
                ->andWhere('a.produit = :produit')
                ->setParameter('campagne', $campagne)
                ->setParameter('produit', $produit)
                ->getQuery()->getResult();
            $qty2 = $em->getRepository('AgriBundle:InterventionProduit')->createQueryBuilder('a')
                ->innerJoin('a.intervention', 'i')
                ->select('SUM(a.qty)')
                ->where('i.campagne = :campagne')
                ->andWhere('a.produit = :produit')
                ->setParameter('campagne', $campagne)
                ->setParameter('produit', $produit)
                ->getQuery()
                ->getSingleScalarResult();
            if(count($achats)>0 || $qty2!=0){
                $qty = 0;
                $complement = 0;
                $qtyprice = 0;
                $price_total = 0;
                foreach($achats as $achat){
                    if($achat->complement != 0){
                        $complement += $achat->complement_total;
                    } else {
                        $qty += $achat->qty;
                    }
                    if($achat->price != 0){
                        $qtyprice += $achat->qty;
                        $price_total += $achat->qty * $achat->price;
                    }
                }
                print("ici ".$qty2);
                if($qty2==null){
                    $qty2 = 0;
                }
                print($qty2);
                $res = new ProduitCampagne();
                $res->produit = $produit;
                $res->campagne = $campagne;
                $res->qty_totale = $qty2;
                $res->stock = $qty - $qty2;
                if($qtyprice > 0){
                    $res->price = ($price_total + $complement)/$qtyprice;
                }
                $em->persist($res);
            }
            $em->flush();
        }
    }

    function get($produit, $campagne){
        $query = $this->createQueryBuilder('p')
            ->where('p.produit = :produit')
            ->andWhere('p.campagne = :campagne')
            ->setParameter('campagne', $campagne)
            ->setParameter('produit', $produit)
            ->getQuery();

        $res = $query->getResult();
        if(count($res) > 0){
            return $res[0];
        }
        return null;
    }

    function getAllForCompany($company){
        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.campagne', 'c')
            ->innerJoin('p.produit', 'p2')
            ->where('c.company = :company')
            ->setParameter('company', $company)
            ->addorderBy('p2.type', 'DESC')
            ->addorderBy('p2.name', 'DESC')
            ->addorderBy('p.campagne', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}