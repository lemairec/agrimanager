<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Robot\Robot;
use App\Entity\Robot\Order;
use App\Entity\Robot\Job;
use App\Form\Robot\JobType;
use App\Form\Robot\JobRobotType;
use DateTime;

class RobotControlleurController extends CommonController
{
    /**
     * @Route("/robots", name="robots")
     **/
    public function robots(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robots = $em->getRepository(Robot::class)->findAll();

        $robots2 = [];
        $now = new DateTime;
        foreach($robots as $r){
            $diffInSeconds = $now->getTimestamp() - $r->last_update->getTimestamp();
            $r->is_connected = ($diffInSeconds < 5*60);
            $robots2[] = $r;
        }
        return $this->render('robot/robots.html.twig', array(
            'robots' => $robots2,
        ));
    }

    /**
     * @Route("/robot/{robot_name}", name="robot")
     **/
    public function robot($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $orders = $em->getRepository("App:Robot\Order")->getLast10ForRobot($robot);
        $jobs = $em->getRepository("App:Robot\Job")->getTop10();
        $data = json_encode($robot->last_data);
        $lat = 0;
        $lng = 0;
        if(array_key_exists("gps_latitude", $robot->last_data)){
            $lat = $robot->last_data["gps_latitude"];
            $lng = $robot->last_data["gps_longitude"];
        };
        return $this->render('robot/robot.html.twig', array(
            'robot_id' => $robot->name,
            'robot_id_bdd' => $robot->id,
            'orders' => $orders,
            'robot' => $robot,
            'robot_data' => $data,
            'lat' => $lat,
            'lng' => $lng,
            'points' => [],
            'jobs' => $jobs
        ));
    }

    /**
     * @Route("/robot_order/{robot_id}/{order_label}", name="robot_order")
     **/
    public function robot_order($robot_id, $order_label, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);
        print($robot->id);

        $order = new Order();
        $order->robot = $robot;
        $order->name = $order_label;
        $order->type = $order_label;
        $order->d_create = new \DateTime();
        $order->params = [];
        //print(json_encode($request->query->all()));
        foreach($request->query->all() as $k => $v){
            if($k == "a_lat" || $k == "a_lon" || $k == "b_lat" || $k == "b_lon" ){
                $order->params[$k] = doubleval($v);
            } else {
                $order->params[$k] = $v;
            }
        }

        //print(json_encode($order->params));

        $em->persist($order);
        $em->flush();
        //return new Response("OK");
        return $this->redirectToRoute('robot', array('robot_name' => $robot_id));
    }

     /**
     * @Route("/robot_jobs", name="robot_jobs")
     **/
    public function robot_jobs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot_jobs = $em->getRepository("App:Robot\Job")->getAll();

        return $this->render('robot/jobs.html.twig', array(
            'jobs' => $robot_jobs,
        ));
    }

    /**
     * @Route("/robot_job/{id}", name="robot_job")
     **/
    public function jobAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_job = $em->getRepository("App:Robot\Job")->find($id);
        if($robot_job == null){
            $robot_job = new Job();
            $robot_job->id = 0;
            $robot_id = $request->query->get("robot_id");
        }
        $robot_job->params_json = json_encode($robot_job->params);
        $form = $this->createForm(JobType::class, $robot_job);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $robot_job->params = json_decode($robot_job->params_json);
            $em->persist($robot_job);
            $em->flush();
            return $this->redirectToRoute('robot_jobs');
        }

        return $this->render('robot/robot_job.html.twig', array(
            'robot_job_id' => $robot_job->id,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/robot_job/{robot_job_id}/delete", name="robot_job_delete")
     **/
    public function deletection($robot_job_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $robot_job= $em->getRepository("App:Robot\Job")->find($robot_job_id);

        $em->remove($robot_job);
        $em->flush();

        return $this->redirectToRoute('robot_jobs');
    }

    /**
     * @Route("/robot_job/{id}/do_it/{robot_id}", name="robot_job_do_it")
     **/
    public function jobDoItAction($id, $robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_job = $em->getRepository("App:Robot\Job")->find($id);
        $robot = $em->getRepository(Robot::class)->find($robot_id);

        $form = $this->createForm(JobRobotType::class, $robot_job);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $order = new Order();
            $order->robot = $robot;
            $order->name = $robot_job->name;
            $order->type = $robot_job->type;
            $order->d_create = new \DateTime();
            $order->params = $robot_job->params;
            $order->params["offset"] = doubleval($robot_job->offset);
            $order->params["inrows"] = $robot_job->inrows;
            $em->persist($order);
            $em->flush();
            //return new Response("OK");
            return $this->redirectToRoute('robot', array('robot_name' => $robot->name));
        }

        return $this->render('robot/robot_job.html.twig', array(
            'robot_job_id' => $robot_job->id,
            'form' => $form->createView()
        ));




    }

     /**
     * @Route("/robot/{robot_id}/delete", name="robot_delete")
     **/
    public function robotActionDelete($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->find($robot_id);

        $orders= $em->getRepository("App:Robot\Order")->findByRobot($robot);
        foreach($orders as $o){
            $em->remove($o);
            $em->flush();
        }

        $em->remove($robot);
        $em->flush();

        return $this->redirectToRoute('robots');
    }

      /**
     * @Route("/robot_job/{robot_id}/clear", name="robot_clear")
     **/
    public function robotActionClear($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot= $em->getRepository(Robot::class)->find($robot_id);


        $orders= $em->getRepository("App:Robot\Order")->findByRobot($robot);
        foreach($orders as $o){
            $em->remove($o);
            $em->flush();
        }

       // $em->remove($robot_job);
       // $em->flush();

        return $this->redirectToRoute('robots');
    }


}
