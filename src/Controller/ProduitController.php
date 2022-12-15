<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Datetime;


use App\Controller\CommonController;

use App\Entity\Produit;
use App\Entity\EphyProduit;
use App\Entity\Intervention;

use App\Form\ProduitType;


class ProduitController extends CommonController
{
    #[Route(path: '/produits', name: 'produits')]
    public function produitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $produits = $em->getRepository(Produit::class)
            ->getAllForCompany($this->company);

        return $this->render('Default/produits.html.twig', array(
            'produits' => $produits,
            'navs' => ["Produits" => "produits"]
        ));
    }

    #[Route(path: '/bilan_produits2', name: 'bilan_produits2')]
    public function bilan_produits2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $produits = $em->getRepository(Produit::class)
            ->getAllForCompany($this->company);
        $campagnes2 = $em->getRepository(Campagne::class)->getAllForCompany($this->getCurrentCampagne($request)->company);

        $produits2 = [];
        foreach($produits as $produit){
            $produit->campagne = [];
            foreach($campagnes2 as $campagne){
                $produitCampagne = $em->getRepository('App:ProduitCampagne')->get($produit,$campagne);
                if($produitCampagne){
                    $produit->campagne[$campagne->name] = $produitCampagne->price;
                } else {
                    $produit->campagne[$campagne->name] = null;
                }
            }
            $produits2[] = $produit;
        }

        return $this->render('Bilan/bilan_produits2.html.twig', array(
            'produits' => $produits2,
            'campagnes2' => $campagnes2,
            'navs' => ["Produits" => "produits"]
        ));
    }

    #[Route(path: '/api/produit', name: 'produit_api')]
    public function produitApiAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $data = $data["produit"];

        $produit = $em->getRepository(Produit::class)->find($data["id"]);
        if($produit){
        } else {
            $produit = new Produit();
            $produit->company = $campagne->company;
        }
        $produit->name = $data["name"];
        $produit->unity = $data["unity"];
        $produit->type = $data["type"];
        $produit->bio = $data["bio"];
        $produit->comment = $data["comment"];
        $produit->engrais_n = $this->parseFloat($data["n"]);
        $produit->engrais_p = $this->parseFloat($data["p"]);
        $produit->engrais_k = $this->parseFloat($data["k"]);
        $produit->engrais_mg = $this->parseFloat($data["mg"]);
        $produit->engrais_so3 = $this->parseFloat($data["s"]);
        $produit->name = $data["name"];
        $produit->ephyProduit = $em->getRepository(EphyProduit::class)->find($data["produit_ephy"]);
        //$intervention->date = DateTime::createFromFormat('d/m/Y', $data["date"]);

        $em->getRepository(Produit::class)->update($produit);

        return new JsonResponse($produit);
    }

    function produitApi(){
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $produit->company = $this->company;
            $em->getRepository(Produit::class)->update($produit);
            return $this->redirectToRoute('produits');
        }
    }

    #[Route(path: '/produit/{produit_id}', name: 'produit')]
    public function produitEdit2Action($produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);
        $interventions = [];
        $achats = [];
        $produitcampagnes = [];
        if($produit_id == '0'){
            $produit = new Produit();
        } else {
            $produit = $em->getRepository(Produit::class)->findOneById($produit_id);
            $interventions = [];//$em->getRepository(Intervention::class)->getAllForProduit($produit);
            $produitcampagnes = [];//$em->getRepository('App:ProduitCampagne')->getAllForProduit($produit);
            $achats = [];//$em->getRepository('App:Achat')->getAllForProduit($produit);
        }

        return $this->render('Default/produit2.html.twig', array(
            'produit' => $produit,
            'ephyProduit' => $produit->ephyProduit,
            'produitcampagnes' => $produitcampagnes,
            'interventions' => $interventions,
            'achats' => $achats,
            'ephy_produits' => $em->getRepository(EphyProduit::class)->getAllActiveWithCommercialesNames(),
            'navs' => ["Produits" => "produits"]
        ));
    }

    #[Route(path: '/produit/{produit_id}/delete', name: 'produit_delete')]
    public function produitDeleteAction($produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Produit::class)->delete($produit_id);
        return $this->redirectToRoute('produits');
    }

    #[Route(path: '/phytos', name: 'phytos')]
    public function phytosAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)->getAllForCompanyPhyto($this->company);
        $sum = 0;
        foreach ($produits as $p) {
            $sum += $p->quantity * $p->price;
        }

        return $this->render('Default/phytos.html.twig', array(
            'produits' => $produits,
            'totalPrice' => $sum,
            'navs' => ["Produits" => "produits", "Stocks" => "stocks"]
        ));
    }

    #[Route(path: '/stocks', name: 'stocks')]
    public function stocksAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)->getAllForCompanyStock($this->company);
        $sum = 0;
        foreach ($produits as $p) {
            $sum += $p->quantity * $p->price;
        }

        return $this->render('Default/produits.html.twig', array(
            'produits' => $produits,
            'totalPrice' => $sum,
            'navs' => ["Produits" => "produits", "Stocks" => "stocks"]
        ));
    }

    #[Route(path: '/stocks2', name: 'stocks2')]
    public function stocks2Action(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)->getAllForCompanyStock($this->company);
        $sum = 0;
        foreach ($produits as $p) {
            $sum += $p->quantity * $p->price;
        }

        return $this->render('Default/produits2.html.twig', array(
            'produits' => $produits,
            'totalPrice' => $sum,
            'navs' => ["Produits" => "produits", "Stocks" => "stocks"]
        ));
    }

    #[Route(path: '/produit_campagnes', name: 'produit_campagnes')]
    public function produitCampagneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)->getAllForCompany($this->getCurrentCampagne($request)->company);
        $campagnes = $em->getRepository(Campagne::class)->getAllForCompany($this->getCurrentCampagne($request)->company);
        foreach($produits as $produit){
            $produit->produit_campagnes = [];
            foreach($campagnes as $campagne){
                $p = $em->getRepository('App:ProduitCampagne')->get($produit, $campagne);
                $produit->produit_campagnes[] = $p;
            }
        }

        return $this->render('Default/produit_campagnes.html.twig', array(
            'produits' => $produits,
            'campagnes2' => $campagnes,
        ));
    }


    #[Route(path: '/engrais', name: 'engrais')]
    public function engraisAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository(Produit::class)
            ->getAllEngraisForCompany($this->company);

        return $this->render('Default/produit_engrais.html.twig', array(
            'produits' => $produits,
        ));
    }
}
