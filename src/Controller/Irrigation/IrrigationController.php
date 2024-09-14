<?php

namespace App\Controller\Irrigation;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

use App\Entity\Company;
use App\Entity\Irrigation\Projet;
use App\Entity\Irrigation\Borne;
use App\Entity\Irrigation\Tuyaux;

use App\Form\Irrigation\ProjetType;
use App\Form\Irrigation\BorneType;
use App\Form\Irrigation\TuyauxType;

//COMPTE
//ECRITURE
//OPERATION


class IrrigationController extends CommonController
{
    public float $a = 6378249.2;
    public float $m_lat_ref_d = 49;
    public float $m_a_cos_lat_ref = 4184507.97916;
    public float $m_lon_ref_d = 4;

    public function calculateBorne($borne){
        $lat = $p[0];
        $lon = $p[1];

        $lat2 = ($lat - $this->m_lat_ref_d)*0.01745329251;
        $lon2 = ($lon - $this->m_lon_ref_d)*0.01745329251;
        
        $x = $this->m_a_cos_lat_ref * $lon2;
        $y = $this->a * $lat2;
    }

    public function calculateTuyaux($tuyaux){
        $tuyaux->longueur = 2;

        $tuyaux->points_xy = [];

        foreach($tuyaux->points as $p){
            $lat = $p[0];
            $lon = $p[1];

            $lat2 = ($lat - $this->m_lat_ref_d)*0.01745329251;
            $lon2 = ($lon - $this->m_lon_ref_d)*0.01745329251;
            
            $x = $this->m_a_cos_lat_ref * $lon2;
            $y = $this->a * $lat2;

            $tuyaux->points_xy[] = [$x, $y];
        }

        $tuyaux->longueur = 0;
        for($i=0; $i < count($tuyaux->points_xy)-1; $i++){
            $x = $tuyaux->points_xy[$i][0];
            $y = $tuyaux->points_xy[$i][1];
            $x2 = $tuyaux->points_xy[$i+1][0];
            $y2 = $tuyaux->points_xy[$i+1][1];

            $l = sqrt(($x2-$x)*($x2-$x)+($y2-$y)*($y2-$y));
            $tuyaux->longueur = $tuyaux->longueur + $l;
        }
    }

    public function populateBornes($tuyaux){
        $this->calculateTuyaux($tuyaux);
    }

    #[Route(path: '/irrigation/irrigations', name: 'irrigations')]
    public function siloBalises(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->check_user($request);
        $projets = $em->getRepository(Projet::class)->findAll();

        return $this->render('Irrigation/projets.html.twig', array(
            'projets' => $projets
        ));
    }

    #[Route(path: '/irrigation/projet/{projet_id}', name: 'irri_projet')]
    public function siloBalise($projet_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $projet = $em->getRepository(Projet::class)->find($projet_id);
        
        if(!$projet){
            $projet = new Projet();
        }

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($projet);
            $em->flush();
            return $this->redirectToRoute('irrigations');
        }
    

        return $this->render('base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route(path: '/irrigation/projet2/{projet_id}', name: 'irri_projet2')]
    public function siloBalise2($projet_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $projet = $em->getRepository(Projet::class)->find($projet_id);

        $bornes = $em->getRepository(Borne::class)->findAll();
        $tuyaux = $em->getRepository(Tuyaux::class)->findByProjet($projet);

        $lat = 49.557595213951515;
        $lon = 3.7736427783966064;

        if(count($tuyaux) > 0){
            $lat = $tuyaux[0]->points[0][0];
            $lon = $tuyaux[0]->points[0][1];
        }

        return $this->render('irrigation/projet.html.twig', array(
            'projet' => $projet,
            'bornes' => $bornes,
            'tuyaux' => $tuyaux,
            'lat' => $lat,
            'lon' => $lon,
        ));
    }

    #[Route(path: '/irrigation/borne/{borne_id}', name: 'irri_borne')]
    public function irriBorne($borne_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $borne = $em->getRepository(Borne::class)->find($borne_id);    
        
        if(!$borne){
            $points = json_decode($request->query->get("points"));
            if(count($points) == 1){
                $pt = $points[0];
                $lat = $pt[0];
                $lon = $pt[1];
                dump($lat);
            }
            $borne = new Borne();
            $borne->lat = $lat;
            $borne->lon = $lon;
        }

        $form = $this->createForm(BorneType::class, $borne);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($borne);
            $em->flush();
            return $this->redirectToRoute('irrigations');
        }
    

        return $this->render('base_form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route(path: '/irrigation/tuyaux/{tuyaux_id}', name: 'irri_tuyaux')]
    public function irriTuyaux($tuyaux_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $tuyaux = $em->getRepository(Tuyaux::class)->find($tuyaux_id);    
        
        if(!$tuyaux){
            $points = json_decode($request->query->get("points"));
            $projet_id = json_decode($request->query->get("projet_id"));
            $projet = $em->getRepository(Projet::class)->find($projet_id);

            $tuyaux = new Tuyaux();
            $tuyaux->points = $points;
            $tuyaux->projet = $projet;
        }

        $form = $this->createForm(TuyauxType::class, $tuyaux);
        $form->handleRequest($request);

        $this->calculateTuyaux($tuyaux);
        
        if ($form->isSubmitted()) {
            $em->persist($tuyaux);
            $em->flush();
            return $this->redirectToRoute('irri_projet2', array('projet_id' => $tuyaux->projet->id));
        }
    
        
        return $this->render('irrigation/tuyaux.html.twig', array(
            'form' => $form->createView(),
            'tuyaux' => $tuyaux
        ));
    }

    #[Route(path: '/irrigation/tuyaux_d/{tuyaux_id}', name: 'irri_tuyaux_delete')]
    public function irriTuyauxD($tuyaux_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $tuyaux = $em->getRepository(Tuyaux::class)->find($tuyaux_id); 
        
        $em->remove($tuyaux);
        $em->flush();
        
        return $this->redirectToRoute('irri_projet2', array('projet_id' => $tuyaux->projet->id));
    }

}
