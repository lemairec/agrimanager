<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Robot\Robot;
use App\Entity\Robot\Order;
use App\Entity\Robot\Passage;

use App\Entity\Robot\Job;

class ApiRobotControlleurController extends CommonController
{
    #[Route(path: '/robot/api/get_order', name: 'robot_api')]
    public function silo_api(Request $request)
    {
        throw "toto";
    }

    #[Route(path: '/robot/api/v2/job')]
    public function post_job_api2(Request $request){
        $em = $this->getDoctrine()->getManager();
        $last_data = $request->request->all();

        $job = new Job();
        $job->name = "test";
        $job->type = "web";
        $job->params = ["points" => json_decode($request->getContent())];
        $em->persist($job);
        $em->flush();

        return new JsonResponse(["OK"]);

    }


    #[Route(path: '/robot/api/v2/post_order')]
    public function post_robot_order(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $last_data = json_decode($request->getContent(), true);

        $robot_id = $last_data['robot_id'];

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);
        if($robot == NULL){
            $robot = new Robot();
            $robot->name = $robot_id;
            $em->persist($robot);
            $em->flush();
        }

        $robot->last_data = $last_data;
        $robot->last_update = new \DateTime();
        $em->persist($robot);
        $em->flush();

        $passage = new Passage();
        $passage->robot = $robot;
        $passage->datetime = new \DateTime();
        $passage->latitude = $last_data["gps_latitude"];
        $passage->longitude = $last_data["gps_longitude"];
        $passage->work = $last_data["work"];
        $passage->l1 = $last_data["l1"];
        $passage->l2 = $last_data["l2"];
        $passage->l3 = $last_data["l3"];
        $passage->l4 = $last_data["l4"];
        if($passage->latitude && $passage->longitude){
            $em->persist($passage);
            $em->flush();
        }

        if($robot->reset){
            $robot->reset = false;
            $em->persist($robot);
            $em->flush();
            return new Response("\$RESET,*");
        }


        $order = $em->getRepository(Order::class)->getDoingForRobot($robot);
        if($order){
            if($order->type == "AVANCE" || $order->type == "AVANCEG" || $order->type == "AVANCED"
                || $order->type == "RECULE" || $order->type == "RECULEG" || $order->type == "RECULED"
                || $order->type == "STOP"
                || $order->type == "MIN_LEFT"|| $order->type == "MAX_LEFT"|| $order->type == "MIN_RIGHT"|| $order->type == "MAX_RIGHT")
            {
                $now = new \DateTime();
                $diffInSeconds = $now->getTimestamp() - $order->d_create->getTimestamp();
                if($diffInSeconds < 0 || $diffInSeconds > 5){
                    $order->status = "done";
                    $em->persist($order);
                    $em->flush();
                    $order = NULL;
                }
            } else {
                $perc = $robot->last_data["perc"];
                $order_id = $order->id; //todo
                if($order->id == $order_id){
                    $order->perc = intval($perc);
                    $em->persist($order);
                    $em->flush();

                    if($perc>99){
                        $order->status = "done";
                        $em->persist($order);
                        $em->flush();
                        $order = NULL;
                    }
                }
            }
        }

        if($order == NULL){
            $order = $em->getRepository(Order::class)->getLastForRobot($robot);
            if($order != NULL){
                $order->status = "doing";
                $em->persist($order);
                $em->flush();
            }
        }

        if($order){
            $data = $order->getEps32();
            return new Response($data);
        }

        return new Response("\$WAIT,*");


    }

    #[Route(path: '/robot/api/v2/post_config')]
    public function post_robot_config(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $last_data = json_decode($request->getContent(), true);
        $robot_id = $last_data['robot_id'];

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);

        return new Response($robot->config);
    }

    #[Route(path: '/robot/api/v2/send_order')]
    public function send_order(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $last_data = $request->request->all();
        $robot_name = $last_data["robot_name"];
        $data_json = $last_data["data"];

        $data = json_decode($data_json);

        $em = $this->getDoctrine()->getManager();

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_name);
        $order = new Order();
        $order->robot = $robot;
        $order->name = $data->name;
        $order->type = $data->type;
        $order->params = $data;
        $order->d_create = new \DateTime();

        $em->persist($order);
        $em->flush();

        return new JsonResponse(["name"=>"","type"=>"SEND_OK"]);
    }

}
