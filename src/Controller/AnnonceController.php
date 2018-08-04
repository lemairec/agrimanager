<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Annonce;

class AnnonceController extends Controller
{
    /**
     * @Route("/annonces", name="annonces")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        if(empty($params["label"])){
            $annonces = $em->getRepository('App:Annonce')->getAllCategories("");
        } else {
            $annonces = $em->getRepository('App:Annonce')->getAll2($params["label"]);
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

        $annonces = $em->getRepository('App:Annonce')->getAllCategories("immobilier");

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

        $annonces = $em->getRepository('App:Annonce')->getBennes();

        return $this->render('Default/annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }
}
