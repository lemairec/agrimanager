<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Robot\Order;
use App\Entity\Robot\Job;
use App\Form\Robot\JobType;
use DateTime;

class RobotControlleurController extends CommonController
{
    /**
     * @Route("/robots", name="robots")
     **/
    public function robots(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robots = $em->getRepository("App:Robot\Robot")->findAll();
        return $this->render('robot/robots.html.twig', array(
            'robots' => $robots,
        ));
    }

    /**
     * @Route("/robot/{robot_id}", name="robot")
     **/
    public function robot($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        $orders = $em->getRepository("App:Robot\Order")->getLast10ForRobot($robot);
        $data = json_encode($robot->last_data);
        $lat = 0;
        $lon = 0;
        if(array_key_exists("gps_latitude", $robot->last_data)){
            $lat = $robot->last_data["gps_latitude"];
            $lon = $robot->last_data["gps_longitude"];
        };
        return $this->render('robot/robot.html.twig', array(
            'robot_id' => $robot_id,
            'orders' => $orders,
            'robot' => $robot,
            'robot_data' => $data,
            'lat' => $lat,
            'lon' => $lon,
            'points' => []
        ));
    }

    /**
     * @Route("/robot_order/{robot_id}/{order_label}", name="robot_order")
     **/
    public function robot_order($robot_id, $order_label, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        print("otot");
        print($robot->id);
        
        $order = new Order();
        $order->robot = $robot;
        $order->name = $order_label;
        $order->d_create = new \DateTime();
        $order->params = $request->query->all();

        $em->persist($order);
        $em->flush();
        return $this->redirectToRoute('robot', array('robot_id' => $robot_id));
    }

    /**
     * @Route("/robot_job/{id}", name="robot")
     **/
    public function jobAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_job = new Job();
        
        $lat = 0;
        $lon = 0;
        $points = [];

        $robot_id = $request->query->get("robot_id");
        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        if(array_key_exists("gps_latitude", $robot->last_data)){
            $lat = $robot->last_data["gps_latitude"];
            $lon = $robot->last_data["gps_longitude"];
        };

        
        /*$tab =  explode ("\n",  $job_gps->job);
        foreach($tab as $t){
            $res = explode(",", $t);
            if(count($res)>2){
                $lat = $res[1];
                $long = $res[2];
                $points[] = ["lat"=>$res[1], "long"=>$res[2]];
            }
        }*/
        
        //dump($tab);
        //dump($lat);

        $form = $this->createForm(JobType::class, $robot_job);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $em->persist($robot_job);
            $em->flush();
            return $this->redirectToRoute('robots');
        }

        return $this->render('robot/robot_job.html.twig', array(
            'form' => $form->createView(),
            'lat' => $lat,
            'long' => $lon,
            'points' => $points
        ));
    }

}
