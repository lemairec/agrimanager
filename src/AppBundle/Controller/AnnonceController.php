<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Annonce;

class DefaultController extends Controller
{
    /**
     * @Route("/annonces", name="annonces")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        if(empty($params["label"])){
            $annonces = $em->getRepository('AppBundle:Annonce')->getAllCategories("");
        } else {
            $annonces = $em->getRepository('AppBundle:Annonce')->getAll2($params["label"]);
        }
        return $this->render('Default/annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }

    /**
     * @Route("/annonces/immobilier", name="annonces_immobilier")
     */
    public function index2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        $annonces = $em->getRepository('AppBundle:Annonce')->getAllCategories("immobilier");

        return $this->render('Default/annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }


    /**
     * @Route("/annonces/bennes", name="bennes")
     */
    public function bennesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        $annonces = $em->getRepository('AppBundle:Annonce')->getBennes();

        return $this->render('Default/annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }
}
