<?php

namespace App\Controller\Silo;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

use App\Entity\Company;
use App\Entity\Silo\Balise;
use App\Entity\Silo\Temperature;

use App\Form\Silo\BaliseType;

//COMPTE
//ECRITURE
//OPERATION


class SiloController extends CommonController
{

    public function addTemperature($em, $t, $balise_str, $company){
        if($t){
            $balise_ = $em->getRepository(Balise::class)->getOrCreate($company, $balise_str);
            $temperature = new Temperature();
            $temperature->temp = $t;
            $temperature->balise = $balise_;
            $temperature->datetime = new DateTime();
            if($t > -100){
                $em->getRepository(Temperature::class)->addTemperature($temperature);
            }
            $balise_->last_temp = $t;
            $balise_->last_update = new DateTime();
            $em->persist($balise_);
            $em->flush();
        }
    }

    #[Route(path: '/silo/api_sonde', name: 'silo_api')]
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $t1 = $request->query->get("t1");
        $t2 = $request->query->get("t2");
        $t3 = $request->query->get("t3");
        $t4 = $request->query->get("t4");
        $te = $request->query->get("te");
        $balise_str = $request->query->get("balise");
        $company = $request->query->get("company");

        $company = $em->getRepository(Company::class)->findOneByName($company);
        if($company == null){
            throw new \Exception("not found Company : ".$company);
        }

        $this->addTemperature($em,$t1,$balise_str."_1", $company);
        $this->addTemperature($em,$t2,$balise_str."_2", $company);
        $this->addTemperature($em,$t3,$balise_str."_3", $company);
        $this->addTemperature($em,$t4,$balise_str."_4", $company);
        $this->addTemperature($em,$te,$balise_str."_e", $company);

        return new Response("ok");
    }


    #[Route(path: '/silo/balises', name: 'silo_balises')]
    public function siloBalises(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $balises = $em->getRepository(Balise::class)->getAllForCompany($this->company);

        $balises_names = [];
        $balises_others = [];

        foreach($balises as $b){
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

    #[Route(path: '/silo/balise/{id}', name: 'silo_balise')]
    public function siloBalise($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $balise = $em->getRepository(Balise::class)->find($id);
        $temperatures = $em->getRepository(Temperature::class)->getAllForBalise($balise);

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
            $chartjs_min['data'][] = ['date' => $temperature->datetime->format("Y-m-d H:i:s"), 'value' => $temperature->temp, 'name' => "" ];
        }
        $chartjss[] = $chartjs_min;
        //dump($chartjss);

        return $this->render('Silo/balise.html.twig', array(
            'form' => $form->createView(),
            'balise' => $balise,
            'temperatures' => $temperatures,
            'chartjss' => $chartjss
        ));
    }

}
