<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use App\Entity\Robot\Robot;
use App\Entity\Robot\Passage;
use App\Entity\Robot\Order;
use App\Entity\Robot\Job;
use App\Form\Robot\JobType;
use App\Form\Robot\JobRobotType;
use App\Form\Robot\RobotType;

use DateTime;

class RobotControlleurController extends CommonController
{
    public function clearRobot($robot_id){
        $em = $this->getDoctrine()->getManager();

        $robot= $em->getRepository(Robot::class)->find($robot_id);

        $i = 0;
        $orders= $em->getRepository(Order::class)->findByRobot($robot);
        foreach($orders as $o){
            $em->remove($o);
            $i=$i+1;
            if($i%20 == 0){
                $em->flush();
            }
        }

        $passages = $em->getRepository(Passage::class)->findByRobot($robot);
        foreach($passages as $p){
            $em->remove($p);
            $i=$i+1;
            if($i%20 == 0){
                $em->flush();
            }
        }
        $em->flush();
    }

    #[Route(path: '/robots', name: 'robots')]
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



    #[Route(path: '/robot/{robot_name}', name: 'robot')]
    public function robot($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $passages = $em->getRepository(Passage::class)->findByRobot($robot);
        //dump($passages);
        $orders = $em->getRepository(Order::class)->getForRobot($robot);
        $jobs = $em->getRepository(Job::class)->getTop10();
        $data = json_encode($robot->last_data);
        $lat = 0;
        $lng = 0;
        if($robot->last_data && array_key_exists("gps_latitude", $robot->last_data)){
            $lat = $robot->last_data["gps_latitude"];
            $lng = $robot->last_data["gps_longitude"];
        };

        $next_order = "---";
        $order = $em->getRepository(Order::class)->getDoingForRobot($robot);
        if($order == NULL){
            $order = $em->getRepository(Order::class)->getLastForRobot($robot);
        }
        if($order){
            $next_order = $order->type." - ".$order->name." - ".$order->status;
        }
        return $this->render('robot/robot.html.twig', array(
            'robot_id' => $robot->name,
            'robot_id_bdd' => $robot->id,
            'orders' => $orders,
            'robot' => $robot,
            'robot_data' => $data,
            'lat' => $lat,
            'lng' => $lng,
            'next_order' => $next_order,
            'passages' => $passages,
            'jobs' => $jobs
        ));
    }

    #[Route(path: '/robot_avance/{robot_name}', name: 'robot_avance')]
    public function robot_avance($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $passages = $em->getRepository(Passage::class)->findByRobot($robot);
        //dump($passages);
        $orders = $em->getRepository(Order::class)->getForRobot($robot);
        $jobs = $em->getRepository(Job::class)->getTop10();
        $data = json_encode($robot->last_data);
        $lat = 0;
        $lng = 0;
        if($robot->last_data && array_key_exists("gps_latitude", $robot->last_data)){
            $lat = $robot->last_data["gps_latitude"];
            $lng = $robot->last_data["gps_longitude"];
        };
        return $this->render('robot/robot_avance.html.twig', array(
            'robot_id' => $robot->name,
            'robot_id_bdd' => $robot->id,
            'orders' => $orders,
            'robot' => $robot,
            'robot_data' => $data,
            'lat' => $lat,
            'lng' => $lng,
            'passages' => $passages,
            'jobs' => $jobs
        ));
    }

    #[Route(path: '/robot_config/{robot_name}', name: 'robot_config')]
    public function robotConfig($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $form = $this->createForm(RobotType::class, $robot);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($robot);
            $em->flush();
            return $this->redirectToRoute('robots');
        }
        return $this->render('base_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/robot_passages/{robot_name}', name: 'robot_passage')]
    public function robot_passages($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $passages = $em->getRepository(Passage::class)->getByRobot($robot);
        return $this->render('robot/robot_passages.html.twig', array(
            'passages' => $passages
        ));
    }

    #[Route(path: '/robot_order/{robot_id}/{order_label}', name: 'robot_order')]
    public function robot_order($robot_id, $order_label, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);

        $em->getRepository(Order::class)->cancelAllOrders($robot);

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

     #[Route(path: '/robot_jobs', name: 'robot_jobs')]
    public function robot_jobs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot_jobs = $em->getRepository(Job::class)->getAll();

        return $this->render('robot/jobs.html.twig', array(
            'jobs' => $robot_jobs,
        ));
    }

    #[Route(path: '/robot_job/{id}', name: 'robot_job')]
    public function jobAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_job = $em->getRepository(Job::class)->find($id);
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
            'robot_esp32' => $robot_job->getEps32(),
            'robot_json' => $robot_job->params_json,
            'form' => $form->createView()
        ));
    }

    #[Route(path: '/robot_job/{robot_job_id}/delete', name: 'robot_job_delete')]
    public function deletection($robot_job_id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $robot_job= $em->getRepository(Job::class)->find($robot_job_id);

        $em->remove($robot_job);
        $em->flush();

        return $this->redirectToRoute('robot_jobs');
    }

    #[Route(path: '/robot_job/{id}/do_it/{robot_id}', name: 'robot_job_do_it')]
    public function jobDoItAction($id, $robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_job = $em->getRepository(Job::class)->find($id);
        $robot = $em->getRepository(Robot::class)->find($robot_id);

        $em->getRepository(Order::class)->cancelAllOrders($robot);

        $this->clearRobot($robot_id);

        if($robot_job->type == "JOBS"){
            foreach($robot_job->params as $p){
                $order = new Order();
                $order->robot = $robot;
                $order->name = $p["name"];
                $order->type = $p["type"];
                $order->d_create = new \DateTime();
                $order->params = $p;
            
                $em->persist($order);
                $em->flush();
            }
        } else {
            $order = new Order();
            $order->robot = $robot;
            $order->name = $robot_job->name;
            $order->type = $robot_job->type;
            $order->d_create = new \DateTime();
            $order->params = $robot_job->params;
            
            $em->persist($order);
            $em->flush();
        }

        return $this->redirectToRoute('robot', array('robot_name' => $robot->name));
    }

    #[Route(path: '/robot/{robot_id}/delete', name: 'robot_delete')]
    public function robotActionDelete($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->find($robot_id);

        $orders= $em->getRepository(Order::class)->findByRobot($robot);
        foreach($orders as $o){
            $em->remove($o);
            $em->flush();
        }

        $em->remove($robot);
        $em->flush();

        return $this->redirectToRoute('robots');
    }

    #[Route(path: '/robot_job/{robot_id}/clear', name: 'robot_clear')]
    public function robotActionClear($robot_id, Request $request)
    {
        $this->clearRobot($robot_id);

        return $this->redirectToRoute('robots');
    }


    #[Route(path: '/robot/{robot_name}/log', name: 'robot_log')]
    public function robotLogClear($robot_name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $passages = $em->getRepository(Passage::class)->findByRobot($robot);
        $str = "";
        foreach($passages as $p){
            $str=$str."\$LINE".$p->l1." ".$p->l2." ".$p->l3." ".$p->l4;
            $str=$str."\n".$p->log;
            $str=$str."\n".$p->log1;
        }
        // Provide a name for your file with extension
        $filename = 'log.txt';
        $fileContent = $str;
        $response = new Response($fileContent);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    #[Route(path: '/robot_job/{robot_id}/reset', name: 'robot_reset')]
    public function robotActionReset($robot_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot= $em->getRepository(Robot::class)->find($robot_id);
        $robot->reset = true;
        $em->persist($robot);
        $em->flush();

        return $this->redirectToRoute('robots');
    }


}
