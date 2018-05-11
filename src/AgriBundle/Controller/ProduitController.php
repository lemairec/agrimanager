<?php

namespace AgriBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Datetime;


use AgriBundle\Controller\CommonController;

use AgriBundle\Entity\Achat;



use AgriBundle\Form\AchatType;
use AgriBundle\Form\DataType;


class ProduitController extends CommonController
{
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

    /*public function achatsDataEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DataType::class);
        $form->handleRequest($request);

        $campagne = $this->getCurrentCampagne($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $em->getRepository('AgriBundle:Achat')->saveCAJData($data['data'], $campagne);
            //return $this->redirectToRoute('achats');
        }
        return $this->render('AgriBundle::base_form.html.twig', array(
            'form' => $form->createView(),
            'campagnes' => $this->campagnes,
            'campagne_id' => $campagne->id,
        ));
    }*/
}
