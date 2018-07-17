<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;
use AgriBundle\Entity\Livraison;

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

    /**
     * @Route("/bilan_produits", name="bilan_produits")
     */
    public function bilanProduitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes2 = $em->getRepository('AgriBundle:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $cultures = [];

        foreach($campagnes2 as $campagne){
            $parcelles = $em->getRepository('AgriBundle:Parcelle')->getAllForCampagneWithoutActive($campagne);

            foreach ($parcelles as $p) {
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = ['culture'=>$p->getCultureName()
                    ,'produits'=>[], 'surface'=>[]];
                    foreach($campagnes2 as $c){
                        $cultures[$p->getCultureName()]["surface"][$c->name] = 0;
                    }
                }
                $cultures[$p->getCultureName()]["surface"][$campagne->name] += $p->surface;
                $culture = $cultures[$p->getCultureName()];

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
                        $produit_name = $produit->produit->type. " - ".$produit->produit->name;
                        if (!array_key_exists($produit_name, $culture["produits"])) {
                            $cultures[$p->getCultureName()]["produits"][$produit_name] = [];
                            foreach($campagnes2 as $c){
                                $cultures[$p->getCultureName()]["produits"][$produit_name][$c->name] = 0;
                            }
                        }
                        $cultures[$p->getCultureName()]["produits"][$produit_name][$campagne->name] += $produit->getQtyHa() * $p->surface * $produit->produit->price;
                    }
                }
                $p->poid_norme = $em->getRepository('AgriBundle:Livraison')->getSumForParcelle($p);



                ksort($cultures[$p->getCultureName()]['produits']);
            }
        }

        return $this->render('AgriBundle:Default:bilan_produits.html.twig', array(
            'campagnes2' => $campagnes2,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/rendements", name="rendements")
     */
    public function bilanRendementsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes2 = $em->getRepository('AgriBundle:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $rendements = [];

        foreach($campagnes2 as $campagne){
            $livraisons = $em->getRepository('AgriBundle:Livraison')->getAllForCampagne($campagne);
            $rendements[$campagne->name] = ['parcelles'=>[], 'cultures'=>[], 'name'=>$campagne->name];
            foreach ($livraisons as $livraison) {
                if($livraison->parcelle){
                    if (!array_key_exists($livraison->parcelle->id, $rendements[$campagne->name]['parcelles'])) {
                        $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id] = ['name'=>$livraison->parcelle->completeName, 'espece' => $livraison->parcelle->culture, 'surface'=>$livraison->parcelle->surface
                        , 'poid' => 0, 'humidite' => 0, 'ps' => 0, 'proteine' => 0, 'calibrage' => 0, 'impurete' => 0];
                    }
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['poid'] += $livraison->poid_norme;
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['humidite'] += $livraison->humidite*$livraison->poid_norme;
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['ps'] += $livraison->ps*$livraison->poid_norme;
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['proteine'] += $livraison->proteine*$livraison->poid_norme;
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['calibrage'] += $livraison->calibrage*$livraison->poid_norme;
                    $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id]['impurete'] += $livraison->impurete*$livraison->poid_norme;
                }
            }
            foreach ($rendements[$campagne->name]['parcelles'] as $key => $value) {
                $rendements[$campagne->name]['parcelles'][$key]['rendement'] = $rendements[$campagne->name]['parcelles'][$key]['poid']/$rendements[$campagne->name]['parcelles'][$key]['surface'];
                $rendements[$campagne->name]['parcelles'][$key]['humidite'] = $rendements[$campagne->name]['parcelles'][$key]['humidite']/$rendements[$campagne->name]['parcelles'][$key]['poid'];
                $rendements[$campagne->name]['parcelles'][$key]['ps'] = $rendements[$campagne->name]['parcelles'][$key]['ps']/$rendements[$campagne->name]['parcelles'][$key]['poid'];
                $rendements[$campagne->name]['parcelles'][$key]['proteine'] = $rendements[$campagne->name]['parcelles'][$key]['proteine']/$rendements[$campagne->name]['parcelles'][$key]['poid'];
                $rendements[$campagne->name]['parcelles'][$key]['calibrage'] = $rendements[$campagne->name]['parcelles'][$key]['calibrage']/$rendements[$campagne->name]['parcelles'][$key]['poid'];
                $rendements[$campagne->name]['parcelles'][$key]['impurete'] = $rendements[$campagne->name]['parcelles'][$key]['impurete']/$rendements[$campagne->name]['parcelles'][$key]['poid'];
                $rendements[$campagne->name]['parcelles'][$key]['caracteristiques'] = Livraison::getStaticCarateristiques($rendements[$campagne->name]['parcelles'][$key]['humidite']
                    , $rendements[$campagne->name]['parcelles'][$key]['ps'], $rendements[$campagne->name]['parcelles'][$key]['proteine'], $rendements[$campagne->name]['parcelles'][$key]['calibrage'], $rendements[$campagne->name]['parcelles'][$key]['impurete']);
            }

            foreach ($livraisons as $livraison) {
                if (!array_key_exists($livraison->espece, $rendements[$campagne->name]['cultures'])) {
                    $rendements[$campagne->name]['cultures'][$livraison->espece] = 0;
                }
                $rendements[$campagne->name]['cultures'][$livraison->espece] += $livraison->poid_norme;
            }
        }

        dump($rendements);
        return $this->render('AgriBundle:Default:bilan_rendements.html.twig', array(
            'campagnes2' => $campagnes2,
            'rendements' => $rendements,
        ));
    }
}
