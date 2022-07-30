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

    public function addTemperature($em, $t, $balise_str, $company){
        

            $balise_ = $em->getRepository("App:Silo\Balise")->getOrCreate($company, $balise_str);
            $temperature = new Temperature();
            $temperature->temp = $t;
            $temperature->balise = $balise_;
            $temperature->datetime = new DateTime();
            $em->getRepository('App:Silo\Temperature')->addTemperature($temperature);
            $balise_->last_update = new DateTime();
            $balise_->last_temp = $t;
            $em->persist($balise_);
            $em->flush();
    }
    
    /**
     * @Route("/silo/api_sonde", name="silo_api")
     **/
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $t1 = $request->query->get("t1");
        $t2 = $request->query->get("t2");
        $t3 = $request->query->get("t3");
        $balise_str = $request->query->get("balise");
        $company = $request->query->get("company");

        $company = $em->getRepository("App:Company")->findOneByName($company);
        if($company == null){
            throw new Exception("not found Company");
        }

        $this->addTemperature($em,$t1,$balise_str."_1", $company);
        $this->addTemperature($em,$t2,$balise_str."_2", $company);
        $this->addTemperature($em,$t3,$balise_str."_3", $company);
        
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

        /*$temp2 = [];
        foreach($temperatures as $temperature){
            $d = $temperature->datetime->format("d-m-y");
            if(!array_key_exists($temp2, $d)){
                $temp2[$d] = ["min"=>]
            }
        }*/

        $chartjs_min = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];
        $chartjs_max = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];

        foreach($temperatures as $temperature){
            $chartjs_min['data'][] = ['date' => $temperature->datetime->format("Y-m-d H:i:s"), 'value' => $temperature->temp, 'name' => "" ];
        }
        $chartjss[] = $chartjs_min;
        //dump($chartjss);
        
        return $this->render('Silo/balise.html.twig', array(
            'balise' => $balise,
            'temperatures' => $temperatures,
            'chartjss' => $chartjss
        ));
    }

}
