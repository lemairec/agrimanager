<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Annonce;

class AnnonceApiController extends Controller
{
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
            $annonce->category = $a->category;
            $annonce->lastView = new \DateTime();
            //print(json_encode($annonce));
            //print("\n");
            if($annonce->price > 10){
                $em->getRepository('App:Annonce')->saveOrUpdate($annonce);
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
