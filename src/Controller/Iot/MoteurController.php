<?php

namespace App\Controller\Iot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

use App\Entity\Company;
use App\Entity\Iot\Iot;
use App\Entity\Iot\Temperature;
use App\Entity\Iot\Moteur;

use App\Form\Iot\MoteurType;

//COMPTE
//ECRITURE
//OPERATION


class MoteurController extends CommonController
{
    public function getMoteur($em, $t, $balise_str, $no, $company, $temp){
        $moteur = $em->getRepository(Moteur::class)->getOrCreate($company, $balise_str.$no);
        /*$temperature = new Temperature();
        $temperature->temp = $t;
        $temperature->balise = $balise_;
        $temperature->datetime = new DateTime();
        if($t > -100){
            $em->getRepository(Temperature::class)->addTemperature($temperature);
        }*/
        $moteur->my_last_value = $t;
        $moteur->last_update = new DateTime();
        $moteur->last_temp = $temp;
        $em->persist($moteur);
        $em->flush();
        return "\$MOT".$no.";ON";
    }

    #[Route(path: '/iot/api_moteur', name: 'api_moteur')]
    public function apiMoteur(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $m1 = $request->query->get("m1");
        $m2 = $request->query->get("m2");
        $m3 = $request->query->get("m3");
        $m4 = $request->query->get("m4");
        $balise_str = $request->query->get("balise");
        $company_str = $request->query->get("company");

        $company = $em->getRepository(Company::class)->findOneByName($company_str);
        if($company == null){
            throw new \Exception("not found Company : ".$company_str.",".$balise_str);
        }
        if($balise_str == null){
            throw new \Exception("not found balise_str : ".$company_str.",".$balise_str);
        }

        $temp = $request->query->get("temp");
        
        if($m1 != NULL){
            $str = $this->getMoteur($em,$m1,$balise_str,"_1", $company, $temp);
        }
        if($m2 != NULL){
            $str = $str."\n".$this->getMoteur($em,$m2,$balise_str,"_2", $company, $temp);
        }
        if($m3 != NULL){
            $str = $str."\n".$this->getMoteur($em,$m3,$balise_str,"_3", $company, $temp);
        }
        if($m4 != NULL){
            $str = $str."\n".$this->getMoteur($em,$m4,$balise_str,"_4", $company, $temp);
        }

        return new Response($str);
    }

    #[Route(path: '/silo/moteurs', name: 'silo_moteurs')]
    public function siloMoteurs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $balises = $em->getRepository(Moteur::class)->getAllForCompany($this->company);

        $balises_names = [];
        $balises_others = [];

        $today = date("d.m.Y");

        foreach($balises as $b){
            $b->is_ok = false;
            $b->last_update = new DateTime();
            if($b->label){
                $balises_names[] = $b;
            } else {
                $balises_others[] = $b;
            }


        }

        return $this->render('Iot/moteurs.html.twig', array(
            'balises_names' => $balises_names,
            'balises_others' => $balises_others
        ));
    }

    #[Route(path: '/silo/moteur/{id}', name: 'silo_moteur')]
    public function siloMoteur($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $duree = $request->query->get('duree');

        $this->check_user($request);
        $moteur = $em->getRepository(Moteur::class)->find($id);

        $moteur->calculate();
        /*$temperatures = $em->getRepository(Temperature::class)->getForBalise($balise, $duree);
        
        if($balise){
            $balise->calculate();
        }*/

        $form = $this->createForm(MoteurType::class, $moteur);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($moteur);
            $em->flush();
            return $this->redirectToRoute('silo_moteurs');
        }

        /*$chartjs_min = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];
        $chartjs_max = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];

        foreach($temperatures as $temperature){
            $temperature->calculate = $balise->calculFor($temperature->temp);
            $chartjs_min['data'][] = ['date' => $temperature->datetime->format("Y-m-d H:i:s"), 'value' => $temperature->calculate, 'name' => "" ];
            
        }*/
        $chartjss[] = ['annee'=> 'min', 'data' => [], 'color' => "", 'hidden' => false];//$chartjs_min;
        $temperatures = [];
        //dump($chartjss);

        return $this->render('Iot/moteur.html.twig', array(
            'form' => $form->createView(),
            'moteur' => $moteur,
            'temperatures' => $temperatures,
            'chartjss' => $chartjss
        ));
    }

}


//http://localhost:8000/iot/api_config?company=dizy&name=iot&version=----&config=$C,MIN,10,TEMP,5,*