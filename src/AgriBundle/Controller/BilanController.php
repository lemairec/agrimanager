<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;


use AgriBundle\Controller\CommonController;


class BilanController extends CommonController
{
    /**
     * @Route("/bilan_detail", name="bilan_detail")
     */
    public function bilanDetailAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagne($campagne);
        foreach ($parcelles as $p) {
            if (!array_key_exists($p->getCultureName(), $cultures)) {
                $cultures[$p->getCultureName()] = 0;
            }
            $cultures[$p->getCultureName()] += $p->surface;

            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($p);
            }
            $p->n = 0;
            $p->p = 0;
            $p->k = 0;
            $p->mg = 0;
            $p->s = 0;
            $p->priceHa = 0;
            foreach($p->interventions as $it){
                $p->priceHa += $it->getPriceHa();
                foreach($it->produits as $produit){
                    $p->n += $produit->getQtyHa() * $produit->produit->n;
                    $p->p += $produit->getQtyHa() * $produit->produit->p;
                    $p->k += $produit->getQtyHa() * $produit->produit->k;
                    $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                    $p->s += $produit->getQtyHa() * $produit->produit->s;
                }
            }
        }
        return $this->render('AgriBundle:Default:bilan_detail.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan", name="bilan")
     */
    public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagneWithoutActive($campagne);

        foreach ($parcelles as $p) {
            if (!array_key_exists($p->getCultureName(), $cultures)) {
                $cultures[$p->getCultureName()] = ['color'=> $p->getCultureColor(),'culture'=>$p->getCultureName(), 'culture'=>$p->getCultureName(),'surface'=>0, 'priceHa'=>0, 'rendement'=>0, 'poid_norme'=>0];
            }


            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($p);
            }
            $p->n = 0;
            $p->p = 0;
            $p->k = 0;
            $p->mg = 0;
            $p->s = 0;
            $p->priceHa = 0;
            foreach($p->interventions as $it){
                $p->priceHa += $it->getPriceHa();
                foreach($it->produits as $produit){
                    $p->n += $produit->getQtyHa() * $produit->produit->n;
                    $p->p += $produit->getQtyHa() * $produit->produit->p;
                    $p->k += $produit->getQtyHa() * $produit->produit->k;
                    $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                    $p->s += $produit->getQtyHa() * $produit->produit->s;
                }
            }
            $p->poid_norme = $em->getRepository('AgriBundle:Livraison')->getSumForParcelle($p);

            $cultures[$p->getCultureName()]['surface'] += $p->surface;
            $cultures[$p->getCultureName()]['priceHa'] += $p->surface*$p->priceHa;
            $cultures[$p->getCultureName()]['rendement'] += $p->surface*$p->rendement;

        }

        $livraisons = $em->getRepository('AgriBundle:Livraison')->getAllForCampagne($campagne);
        foreach ($livraisons as $l) {
            if (array_key_exists($l->parcelle->getCultureName(), $cultures)) {
                $cultures[$l->parcelle->getCultureName()]['poid_norme'] += $l->poid_norme;
            }
        }


        return $this->render('AgriBundle:Default:bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan_charges", name="bilan_charges")
     */
    public function bilan2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagneWithoutActive($campagne);

        foreach ($parcelles as $p) {
            if (!array_key_exists($p->getCultureName(), $cultures)) {
                $cultures[$p->getCultureName()] = ['culture'=>$p->getCultureName()
                ,'surface'=>0, 'priceHa'=>0, 'rendement'=>0, 'poid_norme'=>0
                , 'color'=>$p->getCultureColor(), 'details'=>[]];
            }


            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('AgriBundle:Intervention')->getAllForParcelle($p);
            }
            $p->n = 0;
            $p->p = 0;
            $p->k = 0;
            $p->mg = 0;
            $p->s = 0;
            $p->priceHa = 0;
            $p->details = [];
            foreach($p->interventions as $it){
                $p->priceHa += $it->getPriceHa();
                foreach($it->produits as $produit){
                    $p->n += $produit->getQtyHa() * $produit->produit->n;
                    $p->p += $produit->getQtyHa() * $produit->produit->p;
                    $p->k += $produit->getQtyHa() * $produit->produit->k;
                    $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                    $p->s += $produit->getQtyHa() * $produit->produit->s;
                    if (!array_key_exists($produit->produit->type, $p->details)) {
                        $p->details[$produit->produit->type] = 0;
                    }
                    $p->details[$produit->produit->type]  += $produit->getQtyHa() * $produit->produit->price;
                    if (!array_key_exists($produit->produit->type, $cultures[$p->getCultureName()]['details'])) {
                        $cultures[$p->getCultureName()]['details'][$produit->produit->type] = 0;
                    }
                    $cultures[$p->getCultureName()]['details'][$produit->produit->type] += $produit->getQtyHa() * $p->surface * $produit->produit->price;
                }
                ksort($p->details);

            }
            $p->poid_norme = $em->getRepository('AgriBundle:Livraison')->getSumForParcelle($p);



            $cultures[$p->getCultureName()]['surface'] += $p->surface;
            $cultures[$p->getCultureName()]['priceHa'] += $p->surface*$p->priceHa;
            $cultures[$p->getCultureName()]['rendement'] += $p->surface*$p->rendement;
            ksort($cultures[$p->getCultureName()]['details']);
        }

        $livraisons = $em->getRepository('AgriBundle:Livraison')->getAllForCampagne($campagne);
        foreach ($livraisons as $l) {
            if (array_key_exists($l->parcelle->getCultureName(), $cultures)) {
                $cultures[$l->parcelle->getCultureName()]['poid_norme'] += $l->poid_norme;
            }
        }


        return $this->render('AgriBundle:Default:bilan_charges.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }
}
