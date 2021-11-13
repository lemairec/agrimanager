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
use App\Entity\GpsParcelle;
use App\Form\JobGpsType;

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
     * @Route("/job_gpss/remove_debug", name="job_remove_debug")
     */
    public function jobRemoveDebug(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $job_gpss = $em->getRepository("App:JobGps")->findAll();
        
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

        $job_gps = $em->getRepository("App:JobGps")->find($id);

        
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

        $job_gps = $em->getRepository("App:JobGps")->find($id);

        

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

        
        $res = $em->getRepository("App:JobGps")->findByDateBegin($jobGps->dateBegin);
        foreach($res as $r){
            $em->remove($r);
        }
        $em->flush();

        $em->persist($jobGps);
        $em->flush();

        return new JsonResponse("ok");
    }

    /**
     * @Route("/gps_parcelles", name="gps_parcelles")
     */
    public function parcellesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $company_id = $this->getCurrentCompanyId($request);
        $company = $em->getRepository("App:Company")->find($company_id);
        
        $parcelles_p = $em->getRepository("App:GpsParcelle")->getAllByCompany($company);
        
        return $this->render('Default/gps_parcelles.html.twig', array(
            'parcelles' => $parcelles_p
        ));
    }

    /**
     * @Route("/gps_parcelle/{parcelle_name}", name="gps_parcelle")
     */
    public function parcelleAction($parcelle_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $company_id = $this->getCurrentCompanyId($request);
        $company = $em->getRepository("App:Company")->find($company_id);
        
        $parc = $em->getRepository("App:GpsParcelle")->getActiveByNameCompany($parcelle_name, $company);
        $contour = $parc->data["contour"];
        $lat = $contour[0]["lat"];
        $lon = $contour[0]["lon"];

        $contour[] = $contour[0];

        /*$tab =  explode ("\n",  $job_gps->job);
        $points = [];
        foreach($tab as $t){
            $res = explode(",", $t);
            if(count($res)>2){
                $lat = $res[1];
                $long = $res[2];
                $points[] = ["lat"=>$res[1], "long"=>$res[2]];
            }
        }*/

        return $this->render('Default/gps_parcelle.html.twig', array(
            'parcelle' => $parc,
            'lat' => $lat,
            'lon' => $lon,
            'contour' => $contour
        ));
    }
    
    /**
     * @Route("/api/autosteer/parcelles")
     */
    public function parcellesGpsApiAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $request->query->get("company");
        $company = $em->getRepository("App:Company")->findOneByName($company);
        if($company == null){
            throw new Exception("not found Company");
        }
        
        $parcelles_p = $em->getRepository("App:GpsParcelle")->getAllByCompany($company);
        $parcelles = [];
        foreach($parcelles_p as $p){
            if($p->name){
                $parcelles[] = ["name"=> $p->name, "datetime"=>$p->datetime->format('Y-m-d H:i:s')];
            }
        }
         
        return new JsonResponse($parcelles);
    }

    public function returnParcelle($em, $name, $company){
        $parc = $em->getRepository("App:GpsParcelle")->getActiveByNameCompany($name, $company);
        $parc_d = json_decode(json_encode($parc->data));
        
        $json = ["name"=> $parc->name, "status"=>$parc->status, "datetime"=>$parc->datetime->format('Y-m-d H:i:s')
            , "contour"=>$parc_d->contour, "flag"=>$parc_d->flag, "surface"=>$parc_d->surface];
        return new JsonResponse($json);
    }

    /**
     * @Route("/api/autosteer/parcelle/{name}")
     */
    public function parcelleGpsApiAction($name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $company = $request->query->get("company");
        $company = $em->getRepository("App:Company")->findOneByName($company);
        if($company == null){
            throw new Exception("not found Company");
        }
        
        return $this->returnParcelle($em, $name, $company);
    }

    /**
     * @Route("/api/autosteer/parcelle")
     */
    public function parcelleGpsApiAction2(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->request->get("parcelle"));
        $name = $data->name;
        $company = $request->query->get("company");
        $company = $em->getRepository("App:Company")->findOneByName($company);
        if($company == null){
            throw new Exception("not found Company");
        }

        $parcelles_p = $em->getRepository("App:GpsParcelle")->getAllByNameCompany($data->name, $company);
        foreach($parcelles_p as $p3){
            $p3->active = false;
            $em->persist($p3);
            $em->flush();
        }
        

        $p2 = new GpsParcelle();
        $p2->surface = $data->surface;
        $p2->datetime = new DateTime();
        $p2->data = $data;
        $p2->name = $data->name;
        $p2->status = "ok";
        $p2->active = true;
        $p2->company = $company;
        
        $em->persist($p2);
        $em->flush();

        return $this->returnParcelle($em, $name, $company);
    }
}
