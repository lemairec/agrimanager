<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Datetime;
use App\Entity\Livraison;

use App\Controller\CommonController;

use Dompdf\Dompdf;
use Dompdf\Options;

class BilanController extends CommonController
{
    public function getParcellesForFiches($campagne){
        $em = $this->getDoctrine()->getManager();
        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);
        foreach ($parcelles as $p) {
            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p);
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
        return $parcelles;
    }


    /**
     * @Route("/fiches_parcellaires", name="fiches_parcellaires")
     */
    public function ficheParcellairesAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $parcelles = $this->getParcellesForFiches($campagne);

        $visibility = "visibility: hidden;";
        if($this->getUser()->getUsername() =="lejard"){
            $visibility = "";
        }
        return $this->render('Bilan/fiches_parcellaires.html.twig', array(
            'parcelles' => $parcelles,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'visibility'=> $visibility

        ));
    }

    /**
     * @Route("/fiches_parcellaires_pdf", name="fiches_parcellaires_pdf")
     */
    public function ficheParcellairesPdfAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $parcelles = $this->getParcellesForFiches($campagne);

        $visibility = "visibility: hidden;";
        if($this->getUser()->getUsername() =="lejard"){
            $visibility = "";
        }
        $html = $this->renderView('Bilan/fiches_parcellaires_pdf.html.twig', array(
            'parcelles' => $parcelles,
            'campagne' => $campagne,
            'visibility'=> $visibility

        ));
        //return $this->render('Bilan/fiches_parcellaires_pdf.html.twig', ['parcelles' => $parcelles, 'campagne' => $campagne]);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($pdfOptions);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("fiches_parcellaires.pdf", [
            "Attachment" => false
        ]);

        return new Response("ok");
    }

    /**
     * @Route("/bilan_intervention_prix", name="bilan_intervention_prix")
     */
    public function ficheInterventionPrixAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = $em->getRepository('App:Intervention')->getAllForCampagne($campagne);

        $res = [];
        $sum = 0;
        foreach (array_reverse($interventions) as $it) {
            $price = $it->getPriceHa()*$it->surface;
            $sum += $price;
            $res[] = ["intervention" => $it, "prix" => $price, "sum" => $sum ];
        }
        $res = array_reverse($res);
        $visibility = "visibility: hidden;";
        if($this->getUser()->getUsername() =="lejard"){
            $visibility = "";
        }
        return $this->render('Bilan/bilan_intervention_prix.html.twig', array(
            'res' => $res,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id
        ));
    }

    /*public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);

        foreach ($parcelles as $p) {
            if (!array_key_exists($p->getCultureName(), $cultures)) {
                $cultures[$p->getCultureName()] = ['color'=> $p->getCultureColor(),'culture'=>$p->getCultureName(), 'culture'=>$p->getCultureName(),'surface'=>0, 'priceHa'=>0, 'rendement'=>0, 'poid_norme'=>0];
            }


            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p);
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
            $p->poid_norme = $em->getRepository('App:Livraison')->getSumForParcelle($p);

            $cultures[$p->getCultureName()]['surface'] += $p->surface;
            $cultures[$p->getCultureName()]['priceHa'] += $p->surface*$p->priceHa;
            $cultures[$p->getCultureName()]['rendement'] += $p->surface*$p->rendement;

        }

        $livraisons = $em->getRepository('App:Livraison')->getAllForCampagne($campagne);
        foreach ($livraisons as $l) {
            if (array_key_exists($l->parcelle->getCultureName(), $cultures)) {
                $cultures[$l->parcelle->getCultureName()]['poid_norme'] += $l->poid_norme;
            }
        }


        return $this->render('Bilan/bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }*/

    /**
     * @Route("/bilan", name="bilan")
     */
    public function bilanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $campagnes2 = [];

        foreach($this->campagnes as $campagne){
            $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);

            $cultures = [];
            foreach ($parcelles as $p) {
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = ['color'=> $p->getCultureColor(),'culture'=>$p->getCultureName(), 'culture'=>$p->getCultureName()
                        ,'surface'=>0, 'priceHa'=>0, 'rendement'=>0, 'poid_norme'=>0
                        ,'price_total' => 0];
                }


                $p->interventions = [];
                if($p->id != '0'){
                    $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p);
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
                $p->poid_norme = $em->getRepository('App:Livraison')->getSumForParcelle($p);



                $cultures[$p->getCultureName()]['surface'] += $p->surface;
                $cultures[$p->getCultureName()]['priceHa'] += $p->surface*$p->priceHa;
            }

            $livraisons = $em->getRepository('App:Livraison')->getAllForCampagne($campagne);
            foreach ($livraisons as $l) {
                $cultures[$l->parcelle->getCultureName()]['poid_norme'] += $l->poid_norme;
            }

            $commercialisations = $em->getRepository('App:Commercialisation')->getAllForCampagne($campagne);

            foreach($commercialisations as $commercialisation){
                $cultures[strval($commercialisation->culture)]['price_total'] += $commercialisation->price_total;
            }

            $marge = 0;
            foreach ($cultures as $key => $value) {
                $cultures[$key]['margesHa'] = 0;
                $cultures[$key]['chargesHa'] = $cultures[$key]['priceHa']/$cultures[$key]['surface'];
                $cultures[$key]['rendementHa'] = $cultures[$key]['poid_norme']/$cultures[$key]['surface'];
                if($cultures[$key]['poid_norme'] != 0){
                    $cultures[$key]['price'] = $cultures[$key]['price_total']/$cultures[$key]['poid_norme'];
                    $cultures[$key]['margesHa'] = $cultures[$key]['rendementHa']*$cultures[$key]['price'] - $cultures[$key]['chargesHa'];
                } else {
                    $cultures[$key]['price'] = 0;
                }
                $marge += $cultures[$key]['price_total'] - $cultures[$key]['priceHa'];
            }

            $c = ['parcelles' => $parcelles, 'cultures'=> $cultures, 'campagne'=>$campagne, 'marge'=>$marge];
            $campagnes2[] = $c;


        }

        return $this->render('Bilan/bilan.html.twig', array(
            'campagnes2' => $campagnes2
        ));
    }


    /**
     * @Route("/bilan_engrais", name="bilan_engrais")
     */
    public function bilanEngraisAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagne = $this->getCurrentCampagne($request);

        $cultures = [];

        foreach($this->campagnes as $campagne){
            $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);

            foreach ($parcelles as $p) {
                if (!array_key_exists($p->getCultureName(), $cultures)) {
                    $cultures[$p->getCultureName()] = ['color'=> $p->getCultureColor(),'culture'=>$p->getCultureName(), 'culture'=>$p->getCultureName()
                        ,'campagne' => []];
                }

                if (!array_key_exists($campagne->name, $cultures[$p->getCultureName()]['campagne'])) {
                    $cultures[$p->getCultureName()]['campagne'][$campagne->name] = ['name' => $campagne->name, 'parcelles' => []];
                }

                if($p->id != '0'){
                    $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p, 'ASC');
                }
                $p->n = 0;
                $p->p = 0;
                $p->k = 0;
                $p->mg = 0;
                $p->s = 0;
                $p->priceHa = 0;
                $interventions = [];
                foreach($p->interventions as $it){
                    $n = 0;
                    $s = 0;
                    foreach($it->produits as $produit){
                        $n += $produit->getQtyHa() * $produit->produit->n;
                        $s += $produit->getQtyHa() * $produit->produit->s;
                        $p->n += $produit->getQtyHa() * $produit->produit->n;
                        $p->p += $produit->getQtyHa() * $produit->produit->p;
                        $p->k += $produit->getQtyHa() * $produit->produit->k;
                        $p->mg += $produit->getQtyHa() * $produit->produit->mg;
                        $p->s += $produit->getQtyHa() * $produit->produit->s;

                    }
                    if($n>5){
                        $interventions[] = ['date' => $it->date, 'n'=>$n, 's'=>$s];
                    }
                }
                $parcelle = ['interventions' => $interventions, 'name' => $p->completeName, 'n' => $p->n, 'p' => $p->p, 'k' => $p->k, 'mg' => $p->mg, 's' => $p->s];

                $cultures[$p->getCultureName()]['campagne'][$campagne->name]['parcelles'][] = $parcelle;
            }

        }

        return $this->render('Bilan/bilan_engrais.html.twig', array(
            'cultures' => $cultures
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

        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);

        foreach ($parcelles as $p) {
            if (!array_key_exists($p->getCultureName(), $cultures)) {
                $cultures[$p->getCultureName()] = ['culture'=>$p->getCultureName()
                ,'surface'=>0, 'priceHa'=>0, 'rendement'=>0, 'poid_norme'=>0
                , 'color'=>$p->getCultureColor(), 'details'=>[]];
            }


            $p->interventions = [];
            if($p->id != '0'){
                $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p);
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
            $p->poid_norme = $em->getRepository('App:Livraison')->getSumForParcelle($p);



            $cultures[$p->getCultureName()]['surface'] += $p->surface;
            $cultures[$p->getCultureName()]['priceHa'] += $p->surface*$p->priceHa;
            ksort($cultures[$p->getCultureName()]['details']);
        }

        $livraisons = $em->getRepository('App:Livraison')->getAllForCampagne($campagne);
        foreach ($livraisons as $l) {
            if (array_key_exists($l->parcelle->getCultureName(), $cultures)) {
                $cultures[$l->parcelle->getCultureName()]['poid_norme'] += $l->poid_norme;
            }
        }


        return $this->render('Bilan/bilan_charges.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'parcelles' => $parcelles,
            'cultures' => $cultures,
        ));
    }


    /**
     * @Route("/bilan_dates", name="bilan_dates")
     */
    public function bilanDatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes2 = $em->getRepository('App:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $cultures = [];

        foreach($campagnes2 as $campagne){
            $interventions = $em->getRepository('App:Intervention')->getAllForCampagne($campagne);

            foreach ($interventions as $intervention) {
                $intervention_culture = "";
                foreach($intervention->parcelles as $it){
                    $p = $it->parcelle;
                    $culture = $p->getCultureName();
                    if($intervention_culture != $culture){
                        if (!array_key_exists($p->getCultureName(), $cultures)) {
                            $cultures[$p->getCultureName()] = ['interventions'=>[]];

                        }


                        if (!array_key_exists($intervention->type, $cultures[$p->getCultureName()]["interventions"])) {
                            $cultures[$p->getCultureName()]["interventions"][$intervention->type] = [];
                            foreach($campagnes2 as $c){
                                $cultures[$p->getCultureName()]["interventions"][$intervention->type][$c->name] = ['name' => $intervention->type, 'dates' => []];
                            }
                        }
                        $cultures[$p->getCultureName()]["interventions"][$intervention->type][$campagne->name]["dates"][]
                            = ['date' => $intervention->date, 'id' => $intervention->id, 'name' => $intervention->name];

                        $intervention_culture = $culture;

                    }
                }
            }

        }

        return $this->render('Bilan/bilan_dates.html.twig', array(
            'campagnes2' => $campagnes2,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan_produits", name="bilan_produits")
     */
    public function bilanProduitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes2 = $em->getRepository('App:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $cultures = [];

        foreach($campagnes2 as $campagne){
            $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagneWithoutActive($campagne);

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
                    $p->interventions = $em->getRepository('App:Intervention')->getAllForParcelle($p);
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
                $p->poid_norme = $em->getRepository('App:Livraison')->getSumForParcelle($p);



                ksort($cultures[$p->getCultureName()]['produits']);
            }
        }

        return $this->render('Bilan/bilan_produits.html.twig', array(
            'campagnes2' => $campagnes2,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan_rendements", name="bilan_rendements")
     */
    public function bilanRendementsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagnes2 = $em->getRepository('App:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $rendements = [];
        $cultures = [];

        foreach($campagnes2 as $campagne){
            $livraisons = $em->getRepository('App:Livraison')->getAllForCampagne($campagne);
            $rendements[$campagne->name] = ['parcelles'=>[], 'name'=>$campagne->name];
            foreach ($livraisons as $livraison) {
                if($livraison->parcelle){
                    if (!array_key_exists($livraison->parcelle->id, $rendements[$campagne->name]['parcelles'])) {
                        $rendements[$campagne->name]['parcelles'][$livraison->parcelle->id] = ['name'=>$livraison->parcelle->completeName, 'espece' => $livraison->parcelle->culture, 'color' => $livraison->parcelle->culture->color, 'surface'=>$livraison->parcelle->surface
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

                $culture = $rendements[$campagne->name]['parcelles'][$key]['espece']->__toString();
                if (!array_key_exists($culture, $cultures)) {
                    $cultures[$culture] = [];
                }
                if (!array_key_exists($campagne->name, $cultures[$culture])) {
                    $cultures[$culture][$campagne->name] = ['poid' => 0, 'surface' => 0];
                }
                $cultures[$culture][$campagne->name]['poid'] += $value['poid'];
                $cultures[$culture][$campagne->name]['surface'] += $value['surface'];

            }

            foreach($cultures as $key => $value){
                foreach($cultures[$key] as $key2 => $value2){
                    $cultures[$key][$key2]['rendement'] =  $value2['poid']/$value2['surface'];
                }
            }
        }

        $chartjs_labels = [];
        foreach($cultures as $key => $value){
            $chartjs_labels[] = $key;
        }

        $chartjs_campagnes = [];
        foreach($campagnes2 as $campagne){
            $chartjs_campagne = ['name'=> $campagne->name, 'color'=> $campagne->color, 'data'=> []];
            foreach($cultures as $key => $value){
                if(array_key_exists($key, $cultures)){
                    if(array_key_exists($campagne->name, $cultures[$key])){
                        $chartjs_campagne['data'][]= $cultures[$key][$campagne->name]['rendement'];
                        continue;
                    }
                }
                $chartjs_campagne['data'][]= NULL;
            }
            $chartjs_campagnes[] = $chartjs_campagne;
        }


        return $this->render('Bilan/bilan_rendements.html.twig', array(
            'campagnes2' => $campagnes2,
            'rendements' => $rendements,
            'cultures' => $cultures,
            'chartjs_labels' => $chartjs_labels,
            'chartjs_campagnes' => $chartjs_campagnes,
        ));
    }

    /**
     * @Route("/bilan_comptes", name="bilan_comptes")
     */
    public function comptesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $comptes = $em->getRepository('App:Compte')->getAllForCampagne($campagne);

        $comptes_campagnes = [];
        foreach ($this->campagnes as $campagne) {
            $res = 0;
            foreach ($comptes as $compte) {
                if ($compte->type == 'campagne' || $compte->getPriceCampagne($campagne) != 0){
                    $res = $res + $compte->getPriceCampagne($campagne);
                }
            }
            $comptes_campagnes[$campagne->name] = $res;
        }

        return $this->render('Bilan/bilan_comptes.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'comptes' => $comptes,
            'comptes_campagnes' => $comptes_campagnes
        ));
    }

    public function cmp($line1, $line2) {
        if($line1['date'] == $line2['date']){
            if($line1['type'] == "gasoil"){
                return -1;
            }
            if($line2['type'] == "gasoil"){
                return 1;
            }
        }
        return ($line1['date'] > $line2['date']) ? -1 : 1;
    }

    /**
     * @Route("/bilan_gasoil", name="bilan_gasoil")
     */
    public function bilanGasoilAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagnes2 = $em->getRepository('App:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $lines = [];
        foreach($campagnes2 as $campagne){
            $interventions = $em->getRepository('App:Intervention')->getAllForCampagne($campagne);

            $gasoils = $em->getRepository('App:Gasoil')->getAllForCampagne($campagne);
            foreach($gasoils as $gasoil){
                if($gasoil->litre < 0){
                    $lines[] = ['date' => $gasoil->date, 'type' => 'gasoil', 'litre' => -$gasoil->litre, 'object'=> json_encode($gasoil), 'sumHa' => 0, 'sumL' => 0, 'conso' => 0];
                }
            }

            foreach($interventions as $intervention){
                $lines[] = ['date' => $intervention->date, 'type' => $intervention->type, 'litre' => 0, 'ha' => $intervention->surface, 'object'=> json_encode($intervention), 'sumHa' => 0, 'sumL' => 0, 'conso' => 0];
            }

        }

        uasort($lines, array($this, 'cmp'));
        $lines2 = [];
        foreach ($lines as $line) {
            $lines2[] = $line;
        }

        $sumHa = 0;
        $sumL = 0;
        $prevHa = 0;
        for( $i = count($lines2)-1; $i >= 0 ; --$i){
            if($lines2[$i]["type"] == "gasoil"){
                if($sumHa>0){
                    $prevHa = $sumHa;
                }
                $sumHa = 0;
                $sumL = $sumL + $lines2[$i]["litre"];
                $lines2[$i]["sumL"] = $sumL;
                if($prevHa>0){
                    $lines2[$i]["conso"] = $sumL/$prevHa;
                }
            } else {
                $sumL = 0;
                $sumHa = $sumHa + $lines2[$i]["ha"];
                $lines2[$i]["sumHa"] = $sumHa;
            }

        }


        return $this->render('Bilan/bilan_gasoils.html.twig', array(
            'lines' => $lines2,
        ));
    }
}
