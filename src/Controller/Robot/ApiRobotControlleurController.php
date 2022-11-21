<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Robot\Robot;
use App\Entity\Robot\Order;


class ApiRobotControlleurController extends CommonController
{
    /**
     * @Route("/robot/api/get_order", name="robot_api")
     **/
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_id = $request->query->get("robot_id");

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);
        if($robot == NULL){
            $robot = new Robot();
            $robot->name = $robot_id;
            $em->persist($robot);
            $em->flush();
        }

        $order = $em->getRepository(Order::class)->getLastForRobot($robot);
        $robot->last_data = $request->request->all();
        $robot->last_update = new \DateTime();

        $em->persist($robot);
        $em->flush();
        if($order){
            $now = new \DateTime();
            $diffInSeconds = $now->getTimestamp() - $order->d_create->getTimestamp();
            if($diffInSeconds > 0 && $diffInSeconds < 1000){
                $data = $order->params;
                if($data == null){
                    $data = [];
                }
                $data["name"] = $order->name;
                $data["type"] = $order->type;
                return new JsonResponse($data);
            }
        }

        return new JsonResponse(["name"=>"","type"=>"WAIT"]);
    }

    /**
     * @Route("/robot/api/v2/post_order")
     **/
    public function post_silo_api2(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $robot_id = $request->request->get('robot_id');

        $robot = $em->getRepository(Robot::class)->findOneByName($robot_id);
        if($robot == NULL){
            $robot = new Robot();
            $robot->name = $robot_id;
            $em->persist($robot);
            $em->flush();
        }

        $order = $em->getRepository(Order::class)->getLastForRobot($robot);
        $robot->last_data = $request->request->all();
        $robot->last_update = new \DateTime();

        $em->persist($robot);
        $em->flush();
        if($order){
            $now = new \DateTime();
            $diffInSeconds = $now->getTimestamp() - $order->d_create->getTimestamp();
            if($diffInSeconds > 0 && $diffInSeconds < 20){
                $data = $order->params;
                if($data == null){
                    $data = [];
                }
                $data["name"] = $order->name;
                $data["type"] = $order->type;
               return new JsonResponse($data);
            }
            return new JsonResponse(["name"=>"","type"=>"WAIT", "last_order"=> $order->name, "time"=>$diffInSeconds]);
        }
        return new JsonResponse(["name"=>"","type"=>"WAIT"]);


    }

}
