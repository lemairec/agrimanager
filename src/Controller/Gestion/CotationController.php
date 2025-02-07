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
use App\Entity\Cotation\Cotation;
use App\Entity\Cotation\CotationProduit;
use App\Entity\Gestion\FactureFournisseur;
use App\Entity\Gestion\Compte;

use App\Form\Gestion\CommercialisationType;
use App\Form\Cotation\CotationsCajType;
use App\Form\Cotation\CotationType;
use App\Form\Cotation\CotationProduitType;


//COMPTE
//ECRITURE
//OPERATION


class CotationController extends CommonController
{

    #[Route(path: '/cotations', name: 'cotations')]
    public function cotationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cotations = $em->getRepository(Cotation::class)->getLasts();
        return $this->render('Cotation/cotations.html.twig', array(
            'cotations' => $cotations,
        ));
    }

    #[Route(path: '/cotation_home', name: 'cotation_home')]
    public function cotationHomeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository(CotationProduit::class)->findByCategorie("cereales");
        
        $chartjss = [];
        foreach($produits as $produit){
            $culture = $produit->label;
            $cotations = $em->getRepository(Cotation::class)->getAllProduit($culture);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss[] = ["annee"=>"$produit->name", "color"=> "#".$produit->color, "data"=>$data];
        }

        $produits = $em->getRepository(CotationProduit::class)->findByCategorie("oleagineux");
        $chartjss_o = [];
        foreach($produits as $produit){
            $culture = $produit->label;
            $cotations = $em->getRepository(Cotation::class)->getAllProduit($culture);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss_o[] = ["annee"=>"$produit->name", "color"=> "#".$produit->color, "data"=>$data];
        }

        $produits = $em->getRepository(CotationProduit::class)->findByCategorie("autre");
        $chartjss_ot = [];
        foreach($produits as $produit){
            $culture = $produit->label;
            $cotations = $em->getRepository(Cotation::class)->getAllProduit($culture);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss_ot[] = ["annee"=>"$produit->name", "color"=> "#".$produit->color, "data"=>$data];
        }

        $produits = $em->getRepository(CotationProduit::class)->findByCategorie("c2");
        $chartjss_c2 = [];
        foreach($produits as $produit){
            $culture = $produit->label;
            $cotations = $em->getRepository(Cotation::class)->getAllProduit($culture);
            $data = [];
            foreach ($cotations as $cotation) {
                $data[] = ["date"=>$cotation->date->format('d/m/y'), "value"=>$cotation->value];
            }
            $chartjss_c2[] = ["annee"=>"$produit->name", "color"=> "#".$produit->color, "data"=>$data];
        }

        $cotations = $em->getRepository(Cotation::class)->getLasts();
        return $this->render('Cotation/home.html.twig', array(
            'cotations' => $cotations,
            'chartjss' => $chartjss,
            'chartjss_o' => $chartjss_o,
            'chartjss_ot' => $chartjss_ot,
            'chartjss_c2' => $chartjss_c2,
        ));
    }


    #[Route(path: '/cotations/produits', name: 'cotation_produits')]
    public function cotationProduitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(CotationProduit::class)->findAll();
        return $this->render('Cotation/produits.html.twig', array(
            'produits' => $produits,
        ));
    }


    #[Route(path: '/cotation_produit/{id}', name: 'cotation_produit')]
    public function cotationProduitEditAction($id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        if($id == '0'){
            $produit = new CotationProduit();
        } else {
            $produit = $em->getRepository(CotationProduit::class)->find($id);
        }
        $form = $this->createForm(CotationProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('cotation_produits');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    #[Route(path: '/cotations_all', name: 'cotations_all')]
    public function cotationAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cotations = $em->getRepository(Cotation::class)->getAlls();

        return $this->render('Cotation/cotations.html.twig', array(
            'cotations' => $cotations
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
            if($cotation->produit){
                $cotation->produit_str = $cotation->produit->label;
            }
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
