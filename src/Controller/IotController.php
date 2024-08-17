<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

use App\Entity\Company;
use App\Entity\Iot\Iot;
use App\Entity\Silo\Temperature;

use App\Form\Silo\BaliseType;

//COMPTE
//ECRITURE
//OPERATION


class IotController extends CommonController
{

    #[Route(path: '/iot/api_iot', name: 'iot_api')]
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data1 = $request->query->get("data1");
        $data2 = $request->query->get("data2");
        $data3 = $request->query->get("data3");
        $data4 = $request->query->get("data4");
        $balise_str = $request->query->get("balise");
        $company = $request->query->get("company");

        $company = $em->getRepository(Company::class)->findOneByName($company);
        if($company == null){
            throw new \Exception("not found Company : ".$company.",".$balise_str);
        }

        $this->addTemperature($em,$t1,$balise_str."_1", $company);
        $this->addTemperature($em,$t2,$balise_str."_2", $company);
        $this->addTemperature($em,$t3,$balise_str."_3", $company);
        $this->addTemperature($em,$t4,$balise_str."_4", $company);
        $this->addTemperature($em,$te,$balise_str."_e", $company);

        return new Response("ok");
    }

    #[Route(path: '/iot/api_config', name: 'iot_config')]
    public function config_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $request->query->get("config1");
        $company = $request->query->get("company");

        $company = $em->getRepository(Company::class)->findOneByName($company);
        if($company == null){
            throw new \Exception("not found Company : ".$company.",".$balise_str);
        }

        $iot = $em->getRepository(Iot::class)->getOrCreate($company, $balise_str);
        $iot->last_config = $config;
        $iot->last_update_config = new DateTime();
        $em->getRepository(Iot::class)->save($iot);
            
        return new Response("ok");
    }


    #[Route(path: '/iot/iots', name: 'iots')]
    public function siloBalises(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $iots = $em->getRepository(Iot::class)->getAllForCompany($this->company);

        $balises_names = [];
        $balises_others = [];

        $today = date("d.m.Y");

        foreach($iots as $b){
            $match_date = "";
            if($b->last_update){
                $match_date = $b->last_update->format('d.m.Y');
            }
            $b->is_ok = false;
            if($today == $match_date) {
                if($b->last_temp > -50) {
                    $b->is_ok = true;
                }
            }
            if($b->label){
                $balises_names[] = $b;
            } else {
                $balises_others[] = $b;
            }


        }

        return $this->render('Silo/balises.html.twig', array(
            'balises_names' => $balises_names,
            'balises_others' => $balises_others
        ));
    }

    /*#[Route(path: '/silo/balise/{id}', name: 'silo_balise')]
    public function siloBalise($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $duree = $request->query->get('duree');

        $this->check_user($request);
        $balise = $em->getRepository(Balise::class)->find($id);
        if($duree == "all"){
            $temperatures = $em->getRepository(Temperature::class)->getAllForBalise($balise);
        } else if($duree == "6m"){
            $temperatures = $em->getRepository(Temperature::class)->getAllForBalise6M($balise);
        } else if($duree == "1d"){
            $temperatures = $em->getRepository(Temperature::class)->getAllForBalise1d($balise);
        } else {
            $temperatures = $em->getRepository(Temperature::class)->getAllForBalise2M($balise);
        }
        if($balise){
            $balise->calculate();
        }

        $form = $this->createForm(BaliseType::class, $balise);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($balise);
            $em->flush();
            return $this->redirectToRoute('silo_balises');
        }

        $chartjs_min = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];
        $chartjs_max = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];

        foreach($temperatures as $temperature){
            $temperature->calculate = $balise->calculFor($temperature->temp);
            $chartjs_min['data'][] = ['date' => $temperature->datetime->format("Y-m-d H:i:s"), 'value' => $temperature->calculate, 'name' => "" ];
            
        }
        $chartjss[] = $chartjs_min;
        //dump($chartjss);

        return $this->render('Silo/balise.html.twig', array(
            'form' => $form->createView(),
            'balise' => $balise,
            'temperatures' => $temperatures,
            'chartjss' => $chartjss
        ));
    }*/

}
