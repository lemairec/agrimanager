<?php

namespace App\Controller\Robot;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Robot\Robot;


class ApiRobotControlleurController extends CommonController
{
    /**
     * @Route("/robot/api/get_order", name="robot_api")
     **/
    public function silo_api(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $robot_id = $request->query->get("robot_id");

        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        if($robot == NULL){
            $robot = new Robot();
            $robot->name = $robot_id;
            $em->persist($robot);
            $em->flush();
        }

        $order = $em->getRepository("App:Robot\Order")->getLastForRobot($robot);
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
                $data["order"] = $order->name;
                return new JsonResponse($data);
            }
        }
        
        return new JsonResponse(["order"=>"WAIT"]);
    }

    /**
     * @Route("/robot/api/v2/post_order")
     **/
    public function post_silo_api2(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $robot_id = $request->request->get('robot_id');

        $robot = $em->getRepository("App:Robot\Robot")->findOneByName($robot_id);
        if($robot == NULL){
            $robot = new Robot();
            $robot->name = $robot_id;
            $em->persist($robot);
            $em->flush();
        }

        $order = $em->getRepository("App:Robot\Order")->getLastForRobot($robot);
        $robot->last_data = $request->request->all();
        $robot->last_update = new \DateTime();

        $em->persist($robot);
        $em->flush();
        if($order){
            $now = new \DateTime();
            $diffInSeconds = $now->getTimestamp() - $order->d_create->getTimestamp();
            if($diffInSeconds > 0 && $diffInSeconds < 10){
                $data = $order->params;
                if($data == null){
                    $data = [];
                }
                $data["order"] = $order->name;
                return new JsonResponse($data);
            }
        }
        
        return new JsonResponse(["order"=>"WAIT"]);
    }

}