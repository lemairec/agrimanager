<?php

namespace AgriBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EphyController extends Controller
{
     /**
      * @Route("/ephy_produits", name="ephy_produits")
      */
     public function produitsAction()
     {
         $em = $this->getDoctrine()->getManager();

         $produits = $em->getRepository('AgriBundle:EphyProduit')
             ->createQueryBuilder('p')
             ->add('orderBy','p.name ASC, p.amm ASC')
             ->getQuery()->getResult();

         return $this->render('AgriBundle:Default:ephy_produits.html.twig', array(
             'produits' => $produits,
         ));
     }

     /**
      * @Route("/ephy_produit/{completeName}", name="ephy_produit")
      **/
     public function produitEditAction($completeName)
     {
         $em = $this->getDoctrine()->getManager();
         $produit = $em->getRepository('AgriBundle:EphyProduit')->findOneByCompleteName($completeName);

         return $this->render('AgriBundle:Default:ephy_produit.html.twig', array(
             'produit' => $produit,
         ));
     }
}
