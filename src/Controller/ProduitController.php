<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Datetime;


use App\Controller\CommonController;

use App\Entity\Produit;
use App\Form\ProduitType;


class ProduitController extends CommonController
{
    /**
     * @Route("/produits", name="produits")
     */
    public function produitsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $produits = $em->getRepository('App:Produit')
            ->getAllForCompany($this->company);

        return $this->render('Default/produits.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * @Route("/api/produit", name="produit_api")
     */
    public function produitApiAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        $campagne = $this->getCurrentCampagne($request);

        $data = $data["produit"];

        $produit = $em->getRepository('App:Produit')->find($data["id"]);
        if($produit){
        } else {
            $produit = new Produit();
            $produit->company = $campagne->company;
        }
        $produit->name = $data["name"];
        $produit->unity = $data["unity"];
        $produit->type = $data["type"];
        $produit->n = $data["n"];
        $produit->p = $data["p"];
        $produit->k = $data["k"];
        $produit->mg = $data["mg"];
        $produit->s = $data["s"];
        $produit->name = $data["name"];
        $produit->ephyProduit = $em->getRepository('App:EphyProduit')->find($data["produit_ephy"]);
        //$intervention->date = DateTime::createFromFormat('d/m/Y', $data["date"]);

        $em->getRepository('App:Produit')->update($produit);

        return new JsonResponse($produit);
    }

    function produitApi(){
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $produit->company = $this->company;
            $em->getRepository('App:Produit')->update($produit);
            return $this->redirectToRoute('produits');
        }
    }

    /**
     * @Route("/produit/{produit_id}", name="produit")
     **/
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
            $produit = $em->getRepository('App:Produit')->findOneById($produit_id);
            $interventions = $em->getRepository('App:Intervention')->getAllForProduit($produit);
            $produitcampagnes = $em->getRepository('App:ProduitCampagne')->findByProduit($produit);
            $achats = $em->getRepository('App:Achat')->getAllForProduit($produit);
        }

        return $this->render('Default/produit2.html.twig', array(
            'produit' => $produit,
            'produitcampagnes' => $produitcampagnes,
            'interventions' => $interventions,
            'achats' => $achats,
            'ephy_produits' => $em->getRepository('App:EphyProduit')->findAllActiveWithCommercialesNames()
        ));
    }

    /**
     * @Route("/produit/{produit_id}/delete", name="produit_delete")
     **/
    public function produitDeleteAction($produit_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Produit')->delete($produit_id);
        return $this->redirectToRoute('produits');
    }

    /**
     * @Route("/stocks", name="stocks")
     */
    public function stocksAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('App:Produit')->getAllForCompanyStock($this->company);
        $sum = 0;
        foreach ($produits as $p) {
            $sum += $p->qty * $p->price;
        }
        print($sum);

        return $this->render('Default/produits.html.twig', array(
            'produits' => $produits,
            'totalPrice' => $sum
        ));
    }

    /**
     * @Route("/produit_campagnes", name = "produit_campagnes")
     */
    public function produitCampagneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('App:Produit')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $campagnes = $em->getRepository('App:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
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


    /**
     * @Route("/engrais", name="engrais")
     **/
    public function engraisAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('App:Produit')
            ->getAllEngraisForCompany($this->company);

        return $this->render('Default/produit_engrais.html.twig', array(
            'produits' => $produits,
        ));
    }
}
