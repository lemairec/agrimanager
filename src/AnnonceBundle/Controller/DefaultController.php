<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/annonces")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();
        
        if(empty($params["label"])){
            $annonces = $em->getRepository('AnnonceBundle:Annonce')->getAll();
        } else {
            $annonces = $em->getRepository('AnnonceBundle:Annonce')->getAll2($params["label"]);
        }
        return $this->render('AnnonceBundle:Default:annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }
}
