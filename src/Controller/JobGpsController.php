<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\HttpFoundation\JsonResponse;

use DateTime;
use App\Entity\JobGps;

class JobGpsController extends CommonController
{
    /**
     * @Route("/job_gpss", name="job_gpss")
     */
    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gpss = $em->getRepository("App:JobGps")->findAll();
        
        return $this->render('Default/job_gpss.html.twig', array(
            'job_gpss' => $job_gpss
        ));
    }

    /**
     * @Route("/job_gpss/me", name="job_gpss_me")
     */
    public function jobGpsMeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campagne = $this->getCurrentCampagne($request);
        $user = $this->getUser();
        
        $job_gpss = $em->getRepository("App:JobGps")->findByUser($user);
        
        return $this->render('Default/job_gpss.html.twig', array(
            'job_gpss' => $job_gpss
        ));
    }

    /**
     * @Route("/job_gps/{id}")
     */
    public function jobAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gps = $em->getRepository("App:JobGps")->find($id);

        
        $lat = 0;
        $long = 0;

        $tab =  explode ("\n",  $job_gps->ubx);
        $points = [];
        foreach($tab as $t){
            $res = explode(",", $t);
            if(count($res)>2){
                $lat = $res[1];
                $long = $res[2];
                $points[] = ["lat"=>$res[1], "long"=>$res[2]];
            }
        }
        
        //dump($tab);
        //dump($lat);



        return $this->render('Default/job_gps.html.twig', array(
            'job_gps' => $job_gps,
            'lat' => $lat,
            'long' => $long,
            'points' => $points,
        ));
    }

     /**
     * @Route("/api/job_gps")
     */
    public function annoncesApiAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $request->request->all();



        $jobGps = new JobGps();
        $jobGps->surface = $data["surface"];
        $jobGps->dateBegin = new DateTime($data["date_begin"]);
        $jobGps->dateEnd = new DateTime($data["date_end"]);
        $jobGps->ubx = $data["ubx"];
        $jobGps->userEmail = $data["user_email"];
        $jobGps->user = $em->getRepository("App:User")->findOneByEmail($jobGps->userEmail);

        
        $res = $em->getRepository("App:JobGps")->findByDateBegin($jobGps->dateBegin);
        foreach($res as $r){
            $em->remove($r);
        }
        $em->flush();

        $em->persist($jobGps);
        $em->flush();

        return new JsonResponse("ok");
    }
     
}
