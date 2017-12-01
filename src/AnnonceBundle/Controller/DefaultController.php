<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use AnnonceBundle\Entity\Annonce;

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

    /**
     * @Route("/annonces/api")
     */
    public function annoncesApiAction(Request $request)
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
            //print(json_encode($annonce));
            //print("\n");
            if($annonce->price > 10){
                $em->getRepository('AnnonceBundle:Annonce')->saveOrUpdate($annonce);
            }
        }
        return new Response("ok");
    }

    /**
     * @Route("/annonces/bennes", name="bennes")
     */
    public function bennesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();

        $annonces = $em->getRepository('AnnonceBundle:Annonce')->getBennes();

        return $this->render('AnnonceBundle:Default:annonces.html.twig', array(
            'annonces' => $annonces,
        ));
    }
}
