<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

use App\Entity\Commercialisation;
use App\Entity\Commercialisation\Cotation;


use App\Form\CommercialisationType;
use App\Form\CotationsCajType;

use Symfony\Component\HttpFoundation\File\File;

//COMPTE
//ECRITURE
//OPERATION


class CommercialisationController extends CommonController
{

    /**
     * @Route("/commercialisations", name="commercialisations")
     */
    public function commercialisationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $commercialisations = $em->getRepository('App:Commercialisation')->getAllForCampagne($campagne);

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

        return $this->render('Default/commercialisations.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'commercialisations' => $commercialisations,
            'cultures' => $cultures,
        ));
    }

    /**
     * @Route("/bilan_commercialisations", name="bilan_commercialisations")
     */
    public function bilanCommercialisationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $commercialisations = $em->getRepository('App:Commercialisation')->getAllForCampagne($campagne);
        $parcelles = $em->getRepository('App:Parcelle')->getAllForCampagne($campagne);
        $livraisons = $em->getRepository('App:Livraison')->getAllForCampagne($campagne);

        $cultures = [];
        foreach($parcelles as $parcelle){
            if($parcelle->active){
                if (!array_key_exists(strval($parcelle->culture), $cultures)) {
                    $cultures[strval($parcelle->culture)] = ['culture' => $parcelle->culture, 'qty_estime' => 0, 'qty_livraison' => 0, 'surface' => 0, 'qty_commercialise' => 0, 'price_total_commercialise' => 0, "price" => 0];
                }
                $cultures[strval($parcelle->culture)]['qty_estime'] += $parcelle->surface*$parcelle->culture->getRendementPrev();
                $cultures[strval($parcelle->culture)]['surface'] += $parcelle->surface;
            }
        }

        foreach($commercialisations as $commercialisation){
            if (!array_key_exists(strval($commercialisation->culture), $cultures)) {
                $cultures[strval($commercialisation->culture)] = ['culture' => $commercialisation->culture, 'qty_estime' => 0, 'qty_livraison' => 0,'surface' => 0,'qty_commercialise' => 0, 'price_total_commercialise' => 0, "price" => 0];
            }
            $cultures[strval($commercialisation->culture)]['price_total_commercialise'] += $commercialisation->price_total;
            if($commercialisation->type != "complement"){
                $cultures[strval($commercialisation->culture)]['qty_commercialise'] += $commercialisation->qty;

            }
        }



        foreach($livraisons as $livraison){
            $culture = $livraison->parcelle->culture;
            if (!array_key_exists(strval($culture), $cultures)) {
                $cultures[strval($culture)] = ['culture' => $culture,'qty_estime' => 0, 'qty_livraison' => 0, 'surface' => 0, 'qty_commercialise' => 0, 'price_total_commercialise' => 0, "price" => 0];
            }
            $cultures[strval($livraison->parcelle->culture)]['qty_livraison'] += $livraison->poid_norme;
        }

        $cultures2 = [];
        $camp = $campagne->commercialisation;
        foreach($cultures as $key => $culture){

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

            $culture["qty_commercialise_perc"] = 0;
            if($culture["qty_estime"]){
                $culture["qty_commercialise_perc"] = $culture["qty_commercialise"]/$culture["qty_estime"];
            };

            $cotation = $em->getRepository('App:Commercialisation\Cotation')->getLast('caj',$camp,$culture["culture"]->commercialisation);
            $culture["cotation"] = null;
            $culture["price_today"] = null;
            if($cotation){
                $culture["cotation"] = $cotation->value;
                $culture["price_today"] = ($culture["price_total_commercialise"] + ($culture["qty_estime"]-$culture["qty_commercialise"])*$culture["cotation"])/$culture["qty_estime"];
            }
            $cultures2[] = $culture;
        }
        dump($cultures);
        return $this->render('Default/commercialisations_bilan.html.twig', array(
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'cultures' => $cultures2,
        ));
    }

    /**
     * @Route("/commercialisation/{commercialisation_id}", name="commercialisation")
     **/
    public function commercialisationEditAction($commercialisation_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        if($commercialisation_id == '0'){
            $commercialisation = new Commercialisation();
            $commercialisation->campagne = $campagne;
            $commercialisation->date = new Datetime();
        } else {
            $commercialisation = $em->getRepository('App:Commercialisation')->find($commercialisation_id);
        }
        $cultures = $em->getRepository('App:Culture')->getAllforCompany($this->company);
        $form = $this->createForm(CommercialisationType::class, $commercialisation, array(
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

    /**
     * @Route("/cotations", name="cotations")
     **/
    public function cotationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cotations = $em->getRepository('App:Commercialisation\Cotation')->getLasts();

        return $this->render('Commercialisation/cotations.html.twig', array(
            'cotations' => $cotations,
        ));
    }

    /**
     * @Route("/cotations/caj", name="cotation_caj")
     **/
    public function cotationCajAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CotationsCajType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cotations = $form->getData()["cotations"];
            //dump($cotations);
            $cotations = str_replace("\r", "\t", $cotations);
            $cotations = str_replace("\n", "\t", $cotations);
            $cotations = str_replace(" \t", "\t", $cotations);
            $cotations = str_replace("\t ", "\t", $cotations);
            $cotations = str_replace("\t\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("\t\t", "\t", $cotations);
            $cotations = str_replace("é", "e", $cotations);
            $cotations = str_replace("ï", "i", $cotations);
            $cotations = str_replace(",", ".", $cotations);
            #dump($cotations);

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
            #print(json_encode($cotations)."<br>");
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

                $commercialisation = $em->getRepository('App:Commercialisation\Cotation')->add($cotation);
                return $this->redirectToRoute('cotations');

            }
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
