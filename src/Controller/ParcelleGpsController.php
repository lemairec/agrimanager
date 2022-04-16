<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\HttpFoundation\JsonResponse;

use DateTime;
use App\Entity\JobGps;
use App\Form\JobGpsType;

class ParcelleGpsController extends CommonController
{
    /**
     * @Route("/job_gpss", name="job_gpss")
     */
    public function testAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gpss = $em->getRepository("App:Agrigps\JobGps")->findAll();
        
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
        
        $job_gpss = $em->getRepository("App:Agrigps\JobGps")->findByUser($user);
        
        return $this->render('Default/job_gpss.html.twig', array(
            'job_gpss' => $job_gpss
        ));
    }

    /**
     * @Route("/job_gpss/remove_debug", name="job_remove_debug")
     */
    public function jobRemoveDebug(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gpss = $em->getRepository("App:Agrigps\JobGps")->findAll();
        
        foreach($job_gpss as $j){
            $j->debug = null;
            $em->persist($j);
            $em->flush();
        }
    }


    /**
     * @Route("/job_gps/{id}")
     */
    public function jobAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gps = $em->getRepository("App:Agrigps\JobGps")->find($id);

        
        $lat = 0;
        $long = 0;

        $tab =  explode ("\n",  $job_gps->job);
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

        $form = $this->createForm(JobGpsType::class, $job_gps);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($job_gps);
            $em->flush();
            return $this->redirectToRoute('job_gpss_me');
        }

        return $this->render('Default/job_gps.html.twig', array(
            'form' => $form->createView(),
            'job_gps' => $job_gps,
            'lat' => $lat,
            'long' => $long,
            'points' => $points,
        ));
    }

    /**
     * @Route("/job_gps/{id}/debug")
     */
    public function jobDebugAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gps = $em->getRepository("App:Agrigps\JobGps")->find($id);

        

        // Provide a name for your file with extension
        $filename = 'debug.txt';
        
        // The dinamically created content of the file
        $fileContent = $job_gps->debug;
        
        // Return a response with a specific content
        $response = new Response($fileContent);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    
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
        $jobGps->job = $data["job"];
        $jobGps->debug = $data["debug"];
        $jobGps->userEmail = $data["user_email"];
        $jobGps->user = $em->getRepository("App:User")->findOneByEmail($jobGps->userEmail);

        
        $res = $em->getRepository("App:Agrigps\JobGps")->findByDateBegin($jobGps->dateBegin);
        foreach($res as $r){
            $em->remove($r);
        }
        $em->flush();

        $em->persist($jobGps);
        $em->flush();

        return new JsonResponse("ok");
    }
    
    /**
     * @Route("/api/parcelles_gps")
     */
    public function parcellesGpsApiAction(Request $request)
    {
        $parcelles = [];
        $parcelles[] = ["name"=>"TOTO", "datetime"=>"2020-11-26T20:22:53"];
        return new JsonResponse($parcelles);
    }
}
