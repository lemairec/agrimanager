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
use App\Entity\Iot\MoteurHist;

use App\Form\Iot\MoteurType;

//COMPTE
//ECRITURE
//OPERATION


class MoteurController extends CommonController
{
    public function getMoteur($em, $on_off, $balise_str, $no, $company, $temp){
        $moteur = $em->getRepository(Moteur::class)->getOrCreate($company, $balise_str.$no);
        $moteur->my_last_value = $on_off;
        $moteur->last_update = new DateTime();
        $moteur->last_temperature = $temp;
        $em->persist($moteur);
        $em->flush();
        $moteur->calculate();

        $moteur_hist = new MoteurHist();
        $moteur_hist->moteur = $moteur;
        $moteur_hist->temp_ext = $temp;
        $moteur_hist->on_off = $on_off;
        $moteur_hist->desired_on_off = $moteur->desired;
        $moteur_hist->datetime = new DateTime();
        $moteur_hist->debug = $moteur->debug;
        if($temp > -100){
            $em->getRepository(MoteurHist::class)->addHist($moteur_hist);
        }
        
        if($moteur->desired){
            return "\$MOT".$no.";ON";
        } else {
            return "\$MOT".$no.";OFF";
        }
        
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

        if($moteur == NULL){
            $moteur = new Moteur();
            $moteur->company = $this->company;
        }


        $form = $this->createForm(MoteurType::class, $moteur);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($moteur);
            $em->flush();
            return $this->redirectToRoute('silo_moteurs');
        }

        
        $moteur_hists = $em->getRepository(MoteurHist::class)->getForMoteur($moteur, $duree);
        $chartjs_temp_ext = ['annee'=> 'temp_ext', 'data' => [], 'color' => "red", 'hidden' => false];
        foreach($moteur_hists as $moteur_hist){
            $chartjs_temp_ext['data'][] = ['date' => $moteur_hist->datetime->format("Y-m-d H:i:s"), 'value' => $moteur_hist->temp_ext, 'name' => "" ];
            
        }

        $chartjs_temp_on_off = ['annee'=> 'on_off', 'data' => [], 'color' => "green", 'hidden' => false];
        foreach($moteur_hists as $moteur_hist){
            $chartjs_temp_on_off['data'][] = ['date' => $moteur_hist->datetime->format("Y-m-d H:i:s"), 'value' => $moteur_hist->on_off*10, 'name' => "" ];
            
        }

        $chartjs_balise = ['annee'=> 'balise', 'data' => [], 'color' => "blue", 'hidden' => false];
        
        $temperatures = $em->getRepository(Temperature::class)->getForBalise($moteur->balise, $duree);
        foreach($temperatures as $temperature){
            $temperature->calculate = $moteur->balise->calculFor($temperature->temp);
            $chartjs_balise['data'][] = ['date' => $temperature->datetime->format("Y-m-d H:i:s"), 'value' => $temperature->calculate, 'name' => "" ];
            
        }

        $chartjss = [$chartjs_temp_ext, $chartjs_temp_on_off, $chartjs_balise];
        
        return $this->render('Iot/moteur.html.twig', array(
            'form' => $form->createView(),
            'moteur' => $moteur,
            'temperatures' => $moteur_hists,
            'chartjss' => $chartjss
        ));
    }

    #[Route(path: '/silo/moteur/{id}/delete', name: 'siloo_moteur_delete')]
    public function deleteAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $cotation= $em->getRepository(Moteur::class)->find($id);

        $em->remove($cotation);
        $em->flush();

        return $this->redirectToRoute('silo_moteurs');
    }

}


//http://localhost:8000/iot/api_config?company=dizy&name=iot&version=----&config=$C,MIN,10,TEMP,5,*