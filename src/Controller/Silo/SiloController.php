<?php

namespace App\Controller\Silo;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Entity\Silo\Balise;
use App\Entity\Silo\Temperature;

//COMPTE
//ECRITURE
//OPERATION


class SiloController extends CommonController
{
    
    /**
     * @Route("/silo/api_sonde", name="silo_api")
     **/
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $temp = $request->query->get("temp");
        $temp2 = $request->query->get("temp2");
        $temp3 = $request->query->get("temp3");
        $balise = $request->query->get("balise");
        $company = $request->query->get("company");

        $company = $em->getRepository("App:Company")->findOneByName($company);
        if($company == null){
            throw new Exception("not found Company");
        }

        $balise = $em->getRepository("App:Silo\Balise")->getOrCreate($company, $balise);
        $temperature = new Temperature();
        $temperature->temp = $temp;
        $temperature->temp2 = $temp2;
        $temperature->temp3 = $temp3;
        $temperature->balise = $balise;
        $temperature->datetime = new DateTime();

        $em->getRepository('App:Silo\Temperature')->addTemperature($temperature);
        
        $balise->last_update = new DateTime();
        $balise->last_temp = $temp;
        $balise->last_temp2 = $temp2;
        $balise->last_temp3 = $temp3;

        $em->persist($balise);
        $em->flush();
        
        return new Response("ok");
    }


    /**
     * @Route("/silo/balises", name="silo_balises")
     **/
    public function siloBalises(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $balises = $em->getRepository('App:Silo\Balise')->getAllForCompany($this->company);

        return $this->render('Silo/balises.html.twig', array(
            'balises' => $balises
        ));
    }

    /**
     * @Route("/silo/balise/{id}", name="silo_balise")
     **/
    public function siloBalise($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $balise = $em->getRepository('App:Silo\Balise')->find($id);
        $temperatures = $em->getRepository('App:Silo\Temperature')->getAllForBalise($balise);

        return $this->render('Silo/balise.html.twig', array(
            'balise' => $balise,
            'temperatures' => $temperatures
        ));
    }

}
