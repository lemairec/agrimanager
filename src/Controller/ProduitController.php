<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('App:Produit')
            ->getAllForCompany($this->company);

        return $this->render('Default/produits.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * @Route("/produit/{produit_id}", name="produit")
     **/
    public function produitEditAction($produit_id, Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();
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
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $produit->campagne = $campagne;
            $em->getRepository('App:Produit')->update($produit);
            //return $this->redirectToRoute('produits');
        }
        return $this->render('Default/produit.html.twig', array(
            'form' => $form->createView(),
            'produit' => $produit,
            'produitcampagnes' => $produitcampagnes,
            'interventions' => $interventions,
            'achats' => $achats,
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
            'ephy_produit' => $produit->ephyProduit
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
