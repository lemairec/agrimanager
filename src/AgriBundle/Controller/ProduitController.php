<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use AgriBundle\Controller\CommonController;

use AgriBundle\Entity\Produit;
use AgriBundle\Form\ProduitType;


class ProduitController extends CommonController
{
    /**
     * @Route("/produits", name="produits")
     */
    public function produitsAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')
            ->getAllForCompany($this->company);

        return $this->render('AgriBundle:Default:produits.html.twig', array(
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
            $produit = $em->getRepository('AgriBundle:Produit')->findOneById($produit_id);
            $interventions = $em->getRepository('AgriBundle:Intervention')->getAllForProduit($produit);
            $produitcampagnes = $em->getRepository('AgriBundle:ProduitCampagne')->findByProduit($produit);
            $achats = $em->getRepository('AgriBundle:Achat')->getAllForProduit($produit);
        }
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $produit->campagne = $campagne;
            $em->getRepository('AgriBundle:Produit')->update($produit);
            //return $this->redirectToRoute('produits');
        }
        return $this->render('AgriBundle:Default:produit.html.twig', array(
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
        $em->getRepository('AgriBundle:Produit')->delete($produit_id);
        return $this->redirectToRoute('produits');
    }

    /**
     * @Route("/stocks", name="stocks")
     */
    public function stocksAction(Request $request)
    {
        $campagne = $this->getCurrentCampagne($request);
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')->getAllForCompanyStock($this->company);

        return $this->render('AgriBundle:Default:produits.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * @Route("/produit_campagnes", name = "produit_campagnes")
     */
    public function produitCampagneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('AgriBundle:Produit')->getAllForCompany($this->getCurrentCampagne($request)->company);
        $campagnes = $em->getRepository('AgriBundle:Campagne')->getAllForCompany($this->getCurrentCampagne($request)->company);
        foreach($produits as $produit){
            $produit->produit_campagnes = [];
            foreach($campagnes as $campagne){
                $p = $em->getRepository('AgriBundle:ProduitCampagne')->get($produit, $campagne);
                $produit->produit_campagnes[] = $p;
            }
        }

        return $this->render('AgriBundle:Default:produit_campagnes.html.twig', array(
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

            $produits = $em->getRepository('AgriBundle:Produit')
                ->getAllEngraisForCompany($this->company);

            return $this->render('AgriBundle:Default:produit_engrais.html.twig', array(
                'produits' => $produits,
            ));
    }
}
