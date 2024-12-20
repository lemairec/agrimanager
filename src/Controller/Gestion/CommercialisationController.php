<?php

namespace App\Controller\Gestion;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use DateTime;

use App\Entity\Culture;
use App\Entity\Gestion\Commercialisation;
use App\Entity\Gestion\Cotation;
use App\Entity\Gestion\FactureFournisseur;
use App\Entity\Gestion\Compte;

use App\Form\Gestion\CommercialisationType;
use App\Form\Gestion\CotationsCajType;
use App\Form\Gestion\CotationType;


//COMPTE
//ECRITURE
//OPERATION


class CommercialisationController extends CommonController
{

    #[Route(path: '/commercialisations', name: 'commercialisations')]
    public function commercialisationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $commercialisations = $em->getRepository(Commercialisation::class)->getAllForCampagne($campagne);

        $cultures = [];
        foreach($commercialisations as $commercialisation){
            if (!array_key_exists(strval($commercialisation->culture), $cultures)) {
                $cultures[strval($commercialisation->culture)] = ['qty' => 0, 'price_total' => 0, "price" => 0];
            }
            $cultures[strval($commercialisation->culture)]['price_total'] += $commercialisation->price_total;
            if($commercialisation->type != "complement"){
                $cultures[strval($commercialisation->culture)]['qty'] += $commercialisation->qty;

            }
            if($cultures[strval($commercialisation->culture)]['qty']>0){
                $cultures[strval($commercialisation->culture)]['price'] = $cultures[strval($commercialisation->culture)]['price_total']/$cultures[strval($commercialisation->culture)]['qty'];
            }
        }

        return $this->render('Gestion/commercialisations.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'commercialisations' => $commercialisations,
            'cultures' => $cultures,
        ));
    }

    #[Route(path: '/bilan_commercialisations', name: 'bilan_commercialisations')]
    public function bilanCommercialisationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $commercialisations = $em->getRepository(Commercialisation::class)->getAllForCampagne($campagne);
        $parcelles = $this->getParcellesForFiches($campagne);

        $cultures = [];
        foreach($parcelles as $parcelle){
            if($parcelle->active && $parcelle->culture){
                if (!array_key_exists(strval($parcelle->culture), $cultures)) {
                    $cultures[strval($parcelle->culture)] = ['culture' => $parcelle->culture, 'qty_estime' => 0, 'qty_livraison' => 0, 'surface' => 0, 'qty_commercialise' => 0, 'price_total_commercialise' => 0, "price" => 0, "contrats"=>[]];
                }
                $cultures[strval($parcelle->culture)]['qty_estime'] += $parcelle->surface*$parcelle->culture->getRendementPrev();
                $cultures[strval($parcelle->culture)]['surface'] += $parcelle->surface;
                $cultures[strval($parcelle->culture)]['qty_livraison'] += $parcelle->poid_norme;
        
            }
        }

        foreach($commercialisations as $commercialisation){
            if (!array_key_exists(strval($commercialisation->culture), $cultures)) {
                $cultures[strval($commercialisation->culture)] = ['culture' => $commercialisation->culture, 'qty_estime' => 0, 'qty_livraison' => 0,'surface' => 0,'qty_commercialise' => 0, 'price_total_commercialise' => 0, "price" => 0, "contrats"=>[]];
            }
            $cultures[strval($commercialisation->culture)]['contrats'][] = ["price_total"=>$commercialisation->price_total, "qty"=>$commercialisation->qty, "date"=>$commercialisation->date];
            $cultures[strval($commercialisation->culture)]['price_total_commercialise'] += $commercialisation->price_total;
            if($commercialisation->type != "complement"){
                $cultures[strval($commercialisation->culture)]['qty_commercialise'] += $commercialisation->qty;

            }
        }

        $cultures2 = [];
        $camp = $campagne->commercialisation;
        $total_today = 0;
        $total_realise = 0;
        $total_obj = 0;

        $ecritures_by_campagne_by_tag = [];
        $compte = $em->getRepository(Compte::class)->getByName($this->company, "recolte");
        $factures = $em->getRepository(FactureFournisseur::class)->getAllForCompteCampagne($compte, $campagne);
        $value = 0;
        foreach($factures as $f){
            $str = $f->tag;
            if(!array_key_exists($str, $ecritures_by_campagne_by_tag)){
                $ecritures_by_campagne_by_tag[$str] = [ "value" => $str, "sum" => 0, "facture" => []];
            }
            $ecritures_by_campagne_by_tag[$str]["facture"][] = $f;
            $ecritures_by_campagne_by_tag[$str]["sum"] += -$f->montantHT;
        }
       
        foreach($cultures as $key => $culture){
            

            $total_realise += $culture["price_total_commercialise"];
            if($culture["qty_commercialise"] == 0){
                $culture["price"] = null;
            } else {
                $culture["price"] = $culture["price_total_commercialise"] / $culture["qty_commercialise"];
            }
            $culture["name"] = $key;

            $culture["qty_livraison_perc"] = 0;
            if($culture["qty_estime"]){
                $culture["qty_livraison_perc"] = $culture["qty_livraison"]/$culture["qty_estime"];
            };

            $culture["qty_bilan"] = 0;
            if($culture["surface"] > 0){
                if($culture["qty_livraison"]>0){
                    $culture["qty_bilan"] = $culture["qty_livraison"];
                    $culture["rendement"]=$culture["qty_livraison"]/$culture["surface"];
                } else  {
                    $culture["qty_bilan"] = $culture["qty_estime"];
                    $culture["rendement_prev"]=$culture["qty_estime"]/$culture["surface"];
                }
            }

            $culture["qty_commercialise_perc"] = 0;
            $culture["rendement_prev"]=0;
            if($culture["qty_bilan"]){
                $culture["qty_commercialise_perc"] = $culture["qty_commercialise"]/$culture["qty_bilan"];
            };

            $cotation = $em->getRepository(Cotation::class)->getLast($camp,$culture["culture"]->commercialisation);
            $culture["cotation"] = null;
            $culture["price_today"] = null;
            $culture["price_today_perc"] = null;
            if($cotation){
                $culture["cotation"] = $cotation->value;
                if($culture["qty_bilan"]!=0){
                    $culture["price_today"] = ($culture["price_total_commercialise"] + ($culture["qty_bilan"]-$culture["qty_commercialise"])*$culture["cotation"])/$culture["qty_bilan"];
                }
                $total_today += $culture["price_total_commercialise"] +($culture["qty_bilan"]-$culture["qty_commercialise"])*$culture["cotation"];
                if($culture["culture"]->prixObj){
                    $culture["price_today_perc"] = $culture["cotation"]/$culture["culture"]->prixObj-1.0;
                }
            }
            if($culture["culture"]->prixObj && $culture["culture"]->rendementObj){
                $total_obj += $culture["culture"]->prixObj * $culture["culture"]->rendementObj * $culture["surface"];
            }

            $culture["factures"] = 0;
            $culture["factures_qty"] = 0;
            if(array_key_exists($culture["culture"]->commercialisation, $ecritures_by_campagne_by_tag)){
                $res = $ecritures_by_campagne_by_tag[$culture["culture"]->commercialisation];
                $culture["factures"] = $res["sum"];
                if($culture["qty_livraison"] != 0){
                    $culture["factures_qty"] = $culture["factures"]/$culture["qty_livraison"];
                }
            }
            $cultures2[] = $culture;

        }
        
        //chartjss
        $cultures3 = $em->getRepository(Culture::class)->getAllforCompany($this->company);
        $chartjss = [];
        foreach ($cultures3 as $culture) {
            $cotations = $em->getRepository(Cotation::class)->getAll($campagne->commercialisation,$culture->commercialisation);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss[] = ["annee"=>"$culture", "color"=> $culture->color, "data"=>$data];
        }

        $chartjss2 = [];

        foreach ($cultures2 as $culture) {
            if($culture["factures_qty"] > 10){
                $data[] = ["date"=>"31/12/2023", "value"=>($culture["factures_qty"])];
                $chartjss2[] = ["annee"=>"c_".$culture["culture"],  "color"=> $culture["culture"]->color, "data"=>$data];
            }
        }


        return $this->render('Gestion/commercialisations_bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'cultures' => $cultures2,
            'total_obj' => $total_obj,
            'total_today' => $total_today,
            'total_realise' => $total_realise,
            'chartjss' => $chartjss,
            'chartjss2' => $chartjss2
        ));
    }

    #[Route(path: '/commercialisation/{commercialisation_id}', name: 'commercialisation')]
    public function commercialisationEditAction($commercialisation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($commercialisation_id == '0'){
            $commercialisation = new Commercialisation();
            $commercialisation->campagne = $campagne;
            $commercialisation->date = new Datetime();
        } else {
            $commercialisation = $em->getRepository(Commercialisation::class)->find($commercialisation_id);
        }
        $cultures = $em->getRepository(Culture::class)->getAllforCompany($this->company);
        $factures = $em->getRepository(FactureFournisseur::class)->getAllForCampagne($campagne);
        $form = $this->createForm(CommercialisationType::class, $commercialisation, array(
            'factures' => $factures,
            'cultures' => $cultures
        ));
        $form->handleRequest($request);
        if($commercialisation->qty>0){
            $commercialisation->price = $commercialisation->price_total/$commercialisation->qty;
        }

        if ($form->isSubmitted()) {
            $em->persist($commercialisation);
            $em->flush();
            return $this->redirectToRoute('commercialisations');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/cotations', name: 'cotations')]
    public function cotationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cotations = $em->getRepository(Cotation::class)->getLasts();
        return $this->render('Gestion/cotations.html.twig', array(
            'cotations' => $cotations,
        ));
    }

    #[Route(path: '/cotations_all', name: 'cotations_all')]
    public function cotationAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cotations = $em->getRepository(Cotation::class)->getAlls();

        return $this->render('Gestion/cotations.html.twig', array(
            'cotations' => $cotations
        ));
    }

    #[Route(path: '/cotations_caj', name: 'cotation_caj')]
    public function cotationCajAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CotationsCajType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cotations = $form->getData()["cotations"];
            $cotations = str_replace("\r", "\t", $cotations);
            $cotations = str_replace("\n", "\t", $cotations);
            $cotations = str_replace(" \t", "\t", $cotations);
            $cotations = str_replace("\t ", "\t", $cotations);
            $cotations = str_replace("\t\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("  ", " ", $cotations);
            $cotations = str_replace("é", "e", $cotations);
            $cotations = str_replace("ï", "i", $cotations);
            $cotations = str_replace(",", ".", $cotations);
            print(json_encode($cotations));

            $rows = explode("\t", $cotations);

            $year = "";
            $name = "";
            $values = [];
            $cotations = [];
            foreach($rows as $row){
                if($row == "2017" || $row == "2018"|| $row == "2019"|| $row == "2020"|| $row == "2021"){
                    if(count($values) > 0){
                        $cotations[] = ["year"=> $year, "name"=> $name, "values"=> $values];
                    }
                    $year = $row;
                    $values = [];
                    $name = "";
                } else {
                    $value = floatval($row);
                    if($value != 0){
                        $values[] = $value;
                    } else {
                        if(count($values) > 0){
                            $cotations[] = ["year"=> $year, "name"=> $name, "values"=> $values];
                        }
                        $values = [];
                        $name = $row;
                    }

                }
            }
            if($name != ""){
                $cotations[] = ["year"=> $year, "name"=> $name, "values"=> $values];
            }
            print(json_encode($cotations));
            foreach ($cotations as $row) {
                $cotation = new Cotation();
                $cotation->date = new \DateTime();
                $cotation->campagne = $row["year"];
                $cotation->produit = $row["name"];
                $cotation->source = "caj";
                $values = $row["values"];
                if(count($values) == 2){
                    $cotation->value = $values[0];
                    $cotation->valueStockageEnd = $values[1];
                } else if(count($values) == 3){
                    $cotation->value = $values[0];
                    $cotation->valueStockage = $values[1];
                    $cotation->valueStockageEnd = $values[2];
                }

                $commercialisation = $em->getRepository(Cotation::class)->add($cotation);
                //return $this->redirectToRoute('bilan_commercialisations');

            }
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/cotation/{id}', name: 'cotation')]
    public function cotationEditAction($id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            $cotation = new Cotation();
            $cotation->date = new \DateTime();
        } else {
            $cotation = $em->getRepository(Cotation::class)->find($id);
        }
        $form = $this->createForm(CotationType::class, $cotation);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($cotation);
            $em->flush();
            return $this->redirectToRoute('cotations_all');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/cotation/{id}/delete', name: 'cotation_delete')]
    public function deleteAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $cotation= $em->getRepository(Cotation::class)->find($id);

        $em->remove($cotation);
        $em->flush();

        return $this->redirectToRoute('cotations_all');
    }

    #[Route(path: '/cotations_hist', name: 'cotations_hist')]
    public function cotationHistAction(Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $rep = $em->getRepository(Cotation::class);
        

        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = "SELECT distinct c.produit FROM cotation c WHERE c.date >='2021-12-01 00:00:00';";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $resultSet = $statement->executeQuery();
        $result = $resultSet->fetchAllAssociative();

        $data = []; 
        
        $chartjss = [];
        foreach($result as $r){
            $culture = $r["produit"];
            $cotations = $em->getRepository(Cotation::class)->getAllProduit($culture);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss[] = ["annee"=>"$culture", "color"=> "", "data"=>$data];
        }

        return $this->render('Gestion/commercialisations_bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'cultures' => [],
            'total_obj' => [],
            'total_today' => [],
            'total_realise' => [],
            'chartjss' => $chartjss,
            'chartjss2' => []
        ));
    }

}
