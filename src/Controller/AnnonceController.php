<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Annonce;

use App\Controller\CommonController;

class AnnonceController extends CommonController
{
    /**
     * @Route("/annonces", name="annonces")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $label = $request->request->get("label");
        if(!$label){
            $label = $request->query->get("label");
        }

        if($label){
            $annonces = $em->getRepository('App:Annonce')->getAllCategories("");
        } else {
            $annonces = $em->getRepository('App:Annonce')->getAll2($label);
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
     * @Route("/annonces/immobilier_nantes", name="annonces_immobilier_nantes")
     */
    public function index3Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        $annonces = $em->getRepository('App:Annonce')->getAllCategories("immobilier_nantes");

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

    /**
     * @Route("/annonces/api")
     */
    public function annoncesApiAction(Request $request,  \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $annonces = $request->request->get("annonces");
        $annonces = json_decode($annonces);
        $annonces2 =  [];
        foreach($annonces as $a){
            $annonce = new Annonce();
            $annonce->title = $a->title;
            $annonce->url = $a->url;
            $annonce->type = $a->type;
            $annonce->description = $a->description;
            $annonce->price = $a->price;
            $annonce->clientId = $a->clientId;
            $annonce->image = $a->image;
            $annonce->category = $a->category;
            $annonce->lastView = new \DateTime();
            //print(json_encode($annonce));
            //print("\n");
            if($annonce->price > 10){
                $em->getRepository('App:Annonce')->saveOrUpdate($annonce, $mailer);
            }
        }
        return new Response("ok");
    }

    /**
     * @Route("/annonces/api/update_new")
     */
    public function annoncesApiUpdateNew(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('App:Annonce')->updateNew();
        return new Response("ok");
    }
}
