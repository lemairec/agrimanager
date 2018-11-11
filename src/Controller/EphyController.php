<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class EphyController extends Controller
{
    /**
     * @Route("/ephy_produits", name="ephy_produits")
     */
    public function produitsAction(Request $request)
    {
        $all = intval($request->query->get('all', 0));
        $em = $this->getDoctrine()->getManager();

        return $this->render('Default/ephy_produits.html.twig', array(
            'all' => ($all==1)
        ));
    }

    /**
     * @Route("/ephy_produits_all", name="ephy_produits_all")
     */
    public function produitsAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('App:EphyProduit')->getAllWithCommercialesNames();

        return $this->render('Default/ephy_produits.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * @Route("/api/ephy_produits", name="ephy_produits_api")
     */
    public function produitsApiAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $all = intval($request->query->get('all', 0));

        $produits = $em->getRepository('App:EphyProduit')->getWithCommercialesNamesApi(($all==1));

        return new JsonResponse($produits);
    }

    /**
     * @Route("/ephy_produit/{amm}", name="ephy_produit")
     **/
    public function produitEditAction($amm)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('App:EphyProduit')->find($amm);

        return $this->render('Default/ephy_produit.html.twig', array(
            'produit' => $produit,
        ));
    }

    /**
     * @Route("/ephy_substance/{name}", name="ephy_substance")
     **/
    public function subtanceEditAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $ephy_substance = $em->getRepository('App:EphySubstance')->findOneByName($name);
        $ephy_substanceproduits = $em->getRepository('App:EphySubstanceProduit')->findByEphySubstance($ephy_substance);


        return $this->render('Default/ephy_substance.html.twig', array(
            'ephy_substance' => $ephy_substance,
            'ephy_substanceproduits' => $ephy_substanceproduits
        ));
    }
}
