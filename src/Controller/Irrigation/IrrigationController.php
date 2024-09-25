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
        $lat = $borne->lat;
        $lon = $borne->lon;

        $lat2 = ($lat - $this->m_lat_ref_d)*0.01745329251;
        $lon2 = ($lon - $this->m_lon_ref_d)*0.01745329251;
        
        $borne->m_x = $this->m_a_cos_lat_ref * $lon2;
        $borne->m_y = $this->a * $lat2;
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
        $em = $this->getDoctrine()->getManager();
        $borne = $em->getRepository(Borne::class)->findByProjet($tuyaux->projet);
        foreach($borne as $b){
            $this->calculateBorne($b);
        }

        $this->calculateTuyaux($tuyaux);

        if(!$tuyaux->borneA && count($tuyaux->points_xy) > 1){
            $x = $tuyaux->points_xy[0][0];
            $y = $tuyaux->points_xy[0][1];

            foreach($borne as $b){
                $x2 = $b->m_x;
                $y2 = $b->m_y;

                $l = sqrt(($x2-$x)*($x2-$x)+($y2-$y)*($y2-$y));
                dump($b->name." ".$l." ".$x." ".$x2);
                if($l < 10){
                    $tuyaux->borneA = $b;
                }
            }

            if(!$tuyaux->borneA){
                $tuyaux->borneA = new Borne();
                $tuyaux->borneA->name = $tuyaux->name."_A";
                $tuyaux->borneA->projet = $tuyaux->projet;
                $tuyaux->borneA->lat = $tuyaux->points[0][0];
                $tuyaux->borneA->lon = $tuyaux->points[0][1];
                $this->calculateBorne($tuyaux->borneA);
            }
        }

        if(!$tuyaux->borneB && count($tuyaux->points_xy) > 1){
            $x = $tuyaux->points_xy[count($tuyaux->points_xy)-1][0];
            $y = $tuyaux->points_xy[count($tuyaux->points_xy)-1][1];

            foreach($borne as $b){
                $x2 = $b->m_x;
                $y2 = $b->m_y;

                $l = sqrt(($x2-$x)*($x2-$x)+($y2-$y)*($y2-$y));
                if($l < 10){
                    $tuyaux->borneB = $b;
                }
            }

            if(!$tuyaux->borneB){
                $tuyaux->borneB = new Borne();
                $tuyaux->borneB->name = $tuyaux->name."_B";
                $tuyaux->borneB->projet = $tuyaux->projet;
                $tuyaux->borneB->lat = $tuyaux->points[count($tuyaux->points_xy)-1][0];
                $tuyaux->borneB->lon = $tuyaux->points[count($tuyaux->points_xy)-1][1];
                $this->calculateBorne($tuyaux->borneB);
            }
        }
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

        $bornes = $em->getRepository(Borne::class)->findByProjet($projet);
        $tuyaux = $em->getRepository(Tuyaux::class)->findByProjet($projet);

        $lat = 49.557595213951515;
        $lon = 3.7736427783966064;

        if(count($bornes) > 0){
            $lat_sum = 0;
            $lon_sum = 0;
            foreach($bornes as $b){
                $lat_sum = $lat_sum + $b->lat;
                $lon_sum = $lon_sum + $b->lon;
            }
            $lat = $lat_sum/count($bornes);
            $lon = $lon_sum/count($bornes);
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
            return $this->redirectToRoute('irri_projet2', array('projet_id' => $borne->projet->id));
        }
    

        return $this->render('irrigation/borne.html.twig', array(
            'form' => $form->createView(),
            'borne' => $borne
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
        $this->populateBornes($tuyaux);
        
        if ($form->isSubmitted()) {
            $this->populateBornes($tuyaux);
            $em->persist($tuyaux->borneA);
            $em->persist($tuyaux->borneB);
            $em->flush();
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

    #[Route(path: '/irrigation/borne_d/{borne_id}', name: 'irri_borne_delete')]
    public function irriBorneD($borne_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $borne = $em->getRepository(Borne::class)->find($borne_id); 
        
        $em->remove($borne);
        $em->flush();
        
        return $this->redirectToRoute('irri_projet2', array('projet_id' => $borne->projet->id));
    }

    #[Route(path: '/irrigation/irri_reset/{projet_id}', name: 'irri_reset')]
    public function irriResetD($projet_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $projet = $em->getRepository(Projet::class)->find($projet_id);

        $bornes = $em->getRepository(Borne::class)->findAll();
        foreach($bornes as $b){
            $b->calculate_pression = null;
            $em->persist($b);
        }
        $em->flush();

        return $this->redirectToRoute('irri_projet2', array('projet_id' => $projet->id));
    }

    #[Route(path: '/irrigation/irri_calcul/{projet_id}', name: 'irri_calcul')]
    public function irriCalculD($projet_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->check_user($request);
        $projet = $em->getRepository(Projet::class)->find($projet_id);

        $bornes = $em->getRepository(Borne::class)->findByProjet($projet);
        foreach($bornes as $b){
            if($b->pression){
                $b->calculate_pression = $b->pression;
                $em->persist($b);
            }
        }
        $em->flush();

        for($i = 0; $i < 10; $i++){
            $tuyaux = $em->getRepository(Tuyaux::class)->findByProjet($projet);
            foreach($tuyaux as $t){
                dump($t);
                if($t->borneA->calculate_pression == null){
                    if($t->borneB->calculate_pression != null){
                        $t->borneA->calculate_pression = $t->calculPression($t->borneB->calculate_pression);
                        $em->persist($t->borneB);
                    }
                }
                if($t->borneB->calculate_pression == null){
                    if($t->borneA->calculate_pression != null){
                        $t->borneB->calculate_pression = $t->calculPression($t->borneA->calculate_pression);
                        $em->persist($t->borneB);
                    }
                }
            }
            $em->flush();
        }
        return $this->redirectToRoute('irri_projet2', array('projet_id' => $projet->id));
    }
}
