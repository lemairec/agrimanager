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
use App\Entity\Iot\Sechoir;

use App\Form\Iot\IotType;

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
        $data5 = $request->query->get("data5");
        $data6 = $request->query->get("data6");
        $data7 = $request->query->get("data7");
        $data8 = $request->query->get("data8");
        $balise_str = $request->query->get("balise");
        $company = $request->query->get("company");

        $company = $em->getRepository(Company::class)->findOneByName($company);
        if($company == null){
            throw new \Exception("not found Company : ".$company.",".$balise_str);
        }

        return new Response("ok");
    }

    #[Route(path: '/iot/api_config', name: 'iot_config')]
    public function config_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $request->query->get("config");
        $version = $request->query->get("version");
        $company = $request->query->get("company");
        $name_str = $request->query->get("name");
        
        $company = $em->getRepository(Company::class)->findOneByName($company);
        if($company == null){
            throw new \Exception("not found Company : ".$company);
        }

        $iot = $em->getRepository(Iot::class)->getOrCreate($company, $name_str);
        $iot->last_config = $config;
        $iot->last_version = $version;
        $iot->last_update_config = new DateTime();
        $em->getRepository(Iot::class)->save($iot, true);
            
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

        return $this->render('Iot/iots.html.twig', array(
            'iots_names' => $balises_names,
            'iots_others' => $balises_others
        ));
    }

    #[Route(path: '/iot/iot/{id}', name: 'iot')]
    public function iotBalise($id, Request $request)
    {
        $this->check_user($request);
        $em = $this->getDoctrine()->getManager();
        
        $iot = $em->getRepository(Iot::class)->find($id);
        $form = $this->createForm(IotType::class, $iot);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($iot);
            $em->flush();
            return $this->redirectToRoute('iots');
        }

        return $this->render('Iot/iot.html.twig', array(
            'form' => $form->createView(),
            'iot' => $iot,
            'temperatures' => [],
            'chartjss' => []
        ));
    }

    function getTemperature($data, $key){

    }

    #[Route(path: '/iot/api_sechoir', name: 'api_sechoir')]
    public function apiSechoirBalise(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sechoir = new Sechoir();
        $sechoir->datetime = new Datetime();
        $sechoir->description = $request->query->get("description");
        $sechoir->t_hot = $request->query->get("t_hot");
        $sechoir->t_cons = $request->query->get("t_cons");
        $sechoir->t_out = $request->query->get("t_out");
        $sechoir->bruleur = $request->query->get("bruleur");
        $sechoir->m_hot = $request->query->get("m_hot");
        $sechoir->m_cold = $request->query->get("m_cold");
        $sechoir->nb_cycle = $request->query->get("nb_cycle");

        
        $sechoir->my_data = $request->query->all();

        $em->persist($sechoir);
        $em->flush();

        $sechoirs = $em->getRepository(Sechoir::class)->findAll();
        //dump($sechoirs);

        return new Response("ok");
    }

    #[Route(path: '/sechoir', name: 'sechoir')]
    public function sechoirBalise(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $duree = $request->query->get('duree');

        $sechoirs = $em->getRepository(Sechoir::class)->getAll($duree);
        //dump($sechoirs);

        $chartjss = [];
        
        $data = [];
        foreach ($sechoirs as $sechoir) {
            if($sechoir->t_out && $sechoir->t_out > 0){
                $data[] = ["date"=>$sechoir->datetime->format('d/m/y H:i:s'), "value"=>$sechoir->t_out];
            }
        }
        $chartjss[] = ["annee"=>"out", "color"=> "#6600ff", "data"=>$data];

        $data = [];
        foreach ($sechoirs as $sechoir) {
            if($sechoir->t_hot && $sechoir->t_hot > 0){
                $data[] = ["date"=>$sechoir->datetime->format('d/m/y H:i:s'), "value"=>$sechoir->t_hot];
            }
        }
        $chartjss[] = ["annee"=>"chaud", "color"=> "#ff0066", "data"=>$data];

        //dump($chartjss);

        
        return $this->render('Iot/sechoirs.html.twig', array(
            'sechoirs' => $sechoirs,
            'chartjss' => $chartjss
        ));
    }

}


//http://localhost:8000/iot/api_config?company=dizy&name=iot&version=----&config=$C,MIN,10,TEMP,5,*